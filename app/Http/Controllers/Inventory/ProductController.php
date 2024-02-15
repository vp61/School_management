<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Models\Units;
use App\Models\Brand;
use App\Models\InventoryCategory;
use App\Models\InventoryGst;
use App\Models\Label;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductImage;
use Carbon\Carbon;
use Log,DB,File;

class ProductController extends CollegeBaseController
{
	protected $base_route = 'inventory.product';
    protected $view_path = 'inventory.product';
    protected $panel = 'Product';
    protected $filter_query = [];

    public function __construct(){

    }
    public function index(Request $request){
        
        $data['brand']=Brand::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['brand']=array_prepend($data['brand'],'--Select Brand--','');
        
        $data['product']=Product::select('inventory_products.*','ib.title as brand','ic.title as category','iu.title as unit','sub_cat.title as sub_cat')
        ->leftjoin('inventory_brands as ib','ib.id','=','inventory_products.brand_id')
        ->leftjoin('inventory_categories as ic','ic.id','=','inventory_products.category_id')
        ->leftjoin('inventory_units as iu','iu.id','=','inventory_products.unit_id')
        ->leftjoin('inventory_categories as sub_cat','sub_cat.id','=','inventory_products.sub_category')
        // ->leftjoin('inventory_gst as ig','ig.id','=','inventory_products.gst')
        ->where(function($query)use($request){
            if($request->name){
                $query->where('inventory_products.title','like','%'.$request->name.'%');
            }if($request->start_date && $request->end_date){
                $query->whereBetween('inventory_products.created_at',[$request->start_date.' 00:00:00',$request->end_date.' 00:00:00']);
            }else{
                if(!isset($_GET['start_date'])){
                
                    $query->whereBetween('inventory_products.created_at',[Carbon::now()->format('Y-m-d').' 00:00:00',Carbon::now()->format('Y-m-d').' 23:59:59']); 
                }
            }
            if($request->brand){
              $query->where('inventory_products.brand_id',$request->brand);  
            }
            if($request->sku){
                $query->where('inventory_products.sku',$request->sku);
            }
            if($request->isbn){
                $query->where('inventory_products.isbn',$request->isbn);
            }
            if($request->alert_quantity){
                $query->where('inventory_products.alert_quantity',$request->alert_quantity);
            }
        })->where([
            ['inventory_products.record_status','=',1]
            ])->get();
        // dd($data['product']);
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
    public function add(Request $request){
        $data=$this->dropdowns();
        return view(parent::loadDataToView($this->view_path.'.add'),compact('data'));

    }
    public function store(Request $request)
    {
        $rules=[
            'title'=>'required',
            'unit_id'=>'required',
            'alert_quantity'=>'required',   
            'file[]'=>'image|mimes:jpeg,png,jpg,bmp'
        ];
        $msg=[
            'title.required'=>"Please Enter Title",
            'unit_id.required'=>"Please Select Unit",
            'alert_quantity.required'=>"Please Enter Alert Quantity",
            'file.*.image'=>"Please enter image in format of jpeg, png, bmp, gif, svg, or webp."
        ];
        
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['record_status'=>1]);
        $amount=0;
        if($request->gst && $request->price){
            $gst=$request->gst;
            $price=$request->price;
            $amount=(($gst/100)*$price)+$price;   
        }elseif($request->price){
            $amount=$request->price;
        }
        $request->request->add(['amount'=>$amount]);
        $product=Product::create($request->all());
        if($product->id && (count($request->variation_label)>1)){
            for ($i=1; $i <count($request->variation_label) ; $i++){
            $request->request->add(['value'=>$request->variation_value[$i]]);
            $request->request->add(['product_id'=>$product->id]);
            $request->request->add(['label_id'=>$request->variation_label[$i]]); 
            ProductVariation::create($request->all());
                
            }
        }
        if($request->has('file')){
            $img=$request->file('file');
            if($product->id && (count($img)>0)){ 
                for ($i=0; $i <count($img) ; $i++) { 
                    
                    $imgName='img_'.$i.'-'.Carbon::now()->format('H-i-s').'.'.$img[$i]->getClientOriginalExtension();
                    $dd[]=$img[$i]->move(public_path().'/inventory/'.$product->id.'_'.$product->title.'/',$imgName);
                    $request->request->add(['image_name'=>$imgName]);
                    $request->request->add(['product_id'=>$product->id]);
                    $im=ProductImage::create($request->all());

                }
            } 
        }    
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        $data=$this->dropdowns();
        $data['row']=Product::find($id);
        if(!$data['row']){
            return redirect()->route('inventory.product')->with('message_warning',"Invalid Request!");
        }
        $data['variations']=Product::select('prod_var.label_id','i_label.title as label','prod_var.value','prod_var.id as variation_id')
        ->leftjoin('product_variations as prod_var',function($join){
            $join->on('prod_var.product_id','=','inventory_products.id')
            ->where('prod_var.record_status',1);
        })
        ->leftjoin('inventory_labels as i_label','i_label.id','=','prod_var.label_id')
        ->where([
                ['inventory_products.id','=',$id]
            ])
        ->get();
        $product_id=$data['row']->id;
        $product_title=$data['row']->title;
        $product_path=public_path().'inventory/';
        $data['images']=Product::select('prod_img.id','prod_img.image_name',DB::raw("CONCAT('inventory/','$product_id','_','$product_title','/',prod_img.image_name) as image_path"))
        ->leftjoin('product_image as prod_img',function($join){
            $join->on('prod_img.product_id','=','inventory_products.id')
            ->where('prod_img.record_status',1);
        })
        ->where([
                ['inventory_products.id','=',$id]
            ])
        ->get();
        if($request->all()){
             $rules=[
            'title'=>'required',
            'unit_id'=>'required',
            'alert_quantity'=>'required',   
            'file[]'=>'image|mimes:jpeg,png,jpg,bmp'
        ];
        $msg=[
            'title.required'=>"Please Enter Title",
            'unit_id.required'=>"Please Select Unit",
            'alert_quantity.required'=>"Please Enter Alert Quantity",
            'file.*.image'=>"Please enter image in format of jpeg, png, bmp, gif, svg, or webp."
        ];
        
        $this->validate($request,$rules,$msg);
        $request->request->add(['updated_at'=>Carbon::now()]);
        $request->request->add(['updated_by'=>auth()->user()->id]);
        $amount=0;
        if($request->gst && $request->price){
            $gst=$request->gst;
            $price=$request->price;
            $amount=(($gst/100)*$price)+$price;   
        }elseif($request->price){
            $amount=$request->price;
        }
        $request->request->add(['amount'=>$amount]);
        // $request->request->add(['record_status'=>1]);
        $data['row']->update($request->all());
        if($request->old_label){
          if((count($request->old_label)>0)){
            foreach ($data['variations'] as $key => $value) {
                foreach ($request->old_label as $k => $v) {
                    if($value->variation_id==$k){
                        $variations[]=$k;
                        $variations_row=ProductVariation::find($k);
                        $request->request->add(['label_id'=>$v]);
                        $request->request->add(['value'=>$request->old_value[$k]]);
                        $variations_row->update($request->all());
                       
                    }
                }
            }
            if(isset($variations)){
                ProductVariation::where('product_id',$data['row']->id)
                ->whereNotIn('id',$variations)
                ->update([
                    'record_status'=>0
                ]);
            }
            
        }  
        }
        
        if((count($request->variation_label)>1)){
            for ($i=1; $i <count($request->variation_label) ; $i++){
            $request->request->add(['value'=>$request->variation_value[$i]]);
            $request->request->add(['product_id'=>$data['row']->id]);
            $request->request->add(['label_id'=>$request->variation_label[$i]]); 
            ProductVariation::create($request->all());   
            }
        }
        
        if($request->has('file')){
            $img=$request->file('file');
            if((count($img)>0)){ 
                for ($i=0; $i <count($img) ; $i++) { 
                    $imgName='img_'.$i.'-'.Carbon::now()->format('H-i-s').'.'.$img[$i]->getClientOriginalExtension();
                    $dd[]=$img[$i]->move(public_path().'/inventory/'.$data['row']->id.'_'.$data['row']->title.'/',$imgName);
                    $request->request->add(['image_name'=>$imgName]);
                    $request->request->add(['product_id'=>$data['row']->id]);
                    $im=ProductImage::create($request->all());

                }
            } 
        }
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
        return view(parent::loadDataToView($this->view_path.'.add'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=Product::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }
    public function load_subcategory(Request $request){
        $response=[];
        $response['error']=true;
        if($request->cat){
           $data=InventoryCategory::where([
           ['parent_id','=',$request->cat],
           ['record_status','=',1]
            ])->select('title','id')->get();
           if(count($data)>0){
            $response['data']=$data;
            $response['error']=false;
            $response['msg']="Sub Category Found.";
           }else{
            $response['msg']="Sub Category Not Found.";
           }
        }else{
            $response['msg']="Invalid Request!";
        }
        return response()->json(json_encode($response));
    }
    public function dropdowns(){
        $data['brand']=Brand::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['brand']=array_prepend($data['brand'],'--Select Brand--','');
        $data['category']=InventoryCategory::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['category']=array_prepend($data['category'],'--Select Category--','');
        $data['unit']=Units::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['unit']=array_prepend($data['unit'],'--Select Unit--','');
        $data['label']=Label::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['label']=array_prepend($data['label'],'--Select Label--','');
        $data['gst']=InventoryGst::where('record_status',1)->select('value','title')->pluck('title','value')->toArray();
        $data['gst']=array_prepend($data['gst'],'--Select G.S.T.--','');
        return $data;
    }
    public function remove_image(Request $request){
        $response=[];
        $response['error']=true;
        if($request->image_id){
            $data=DB::table('product_image')->select('ip.title','product_id','image_name')
            ->leftjoin('inventory_products as ip','ip.id','=','product_image.product_id')
            ->where('product_image.id',$request->image_id)
            ->first();
            $path=public_path().'/inventory/'.$data->product_id.'_'.$data->title.'/'.$data->image_name;
            if (File::exists($path)) {
                unlink($path);
                $row=ProductImage::find($request->image_id);
               $status=$row->delete();
               $response['error']=false;
               $response['success']="Image deleted successfully.";
            }else{
                $response['msg']="No such image. Please refresh & try again.";
            }
        }else{
            $response['msg']="Invalid Request!";
        }
        return response()->json(json_encode($response));
    }
}
