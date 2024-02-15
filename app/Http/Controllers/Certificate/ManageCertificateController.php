<?php

namespace App\Http\Controllers\Certificate;

use Illuminate\Http\Request;
use App\Http\Controllers\CollegeBaseController;
use App\Models\Faculty;
use Session,DB,Carbon;

class ManageCertificateController extends CollegeBaseController
{
    protected $base_route = 'certificate';
    protected $view_path = 'certificate';
    protected $panel = 'Certificate';
    protected $filter_query = [];

    public function __construct()
    {

    }
    public function index(Request $request){
        $data['values']=DB::table('certificates')->select('id','title')->where('status',1)->get();
        return view(parent::loadDataToView($this->view_path.'.manage.index'),compact('data'));
    }
    public function store(Request $request){
        $data['certificate']=DB::table('certificates')->insert([
            ['title'=>$request->title,'body'=>$request->body,'created_at'=>Carbon\Carbon::now(),'status'=>1,'created_by'=>auth()->user()->id]

        ]);
        return redirect('certificate/manage')->with('message_success','Certificate created, edit certificate to add more information ');
    }
    public function edit($id){
    	
       	$data['value']=DB::table('certificates')->select('*')->where('id',$id)->first();
    	return view(parent::loadDataToView($this->view_path.'.edit.index'),compact('data'));
    }
    public function update(Request $request,$id){
    	
    	$std_img=!empty($request->req_img)?1:0;
    	  $img_path = public_path().DIRECTORY_SEPARATOR.'certificate'.DIRECTORY_SEPARATOR;
    	  $header_image_name=null;
    	  $bg_img_name=null;
        if ($request->hasFile('header_img')){
            $header_img = $request->file('header_img');
            $header_image_name = $id.'header.'.$header_img->getClientOriginalExtension();
            $header_img->move($img_path, $header_image_name);
        }
        if($request->hasFile('bg_img')){
        	$bg_img=$request->file('bg_img');
        	$bg_img_name=$id.'background.'.$bg_img->getClientOriginalExtension();
        	$bg_img->move($img_path, $bg_img_name);
        }
    	$insert=DB::table('certificates')->updateOrInsert(
    		['id'=>$id],
    		['created_at'=>Carbon\Carbon::now(),'created_by'=>auth()->user()->id,'title'=>$request->name,'left_header'=>$request->left_header,'center_header'=>$request->center_header,'right_header'=>$request->right_header,'body'=>$request->body,'body_height'=>$request->body_height,'body_ptop'=>$request->body_padding_top,'body_pbottom'=>$request->body_padding_bottom,'body_pleft'=>$request->body_padding_left,'body_pright'=>$request->body_padding_right,'left_footer'=>$request->left_footer,'center_footer'=>$request->center_footer,'right_footer'=>$request->right_footer,'header_img'=>$header_image_name,'header_img_height'=>$request->header_img_height,'header_img_ptop'=>$request->header_padding_top,'header_img_pbottom'=>$request->header_padding_bottom,'header_img_pleft'=>$request->header_padding_left,'header_img_pright'=>$request->header_padding_right,'bg_img'=>$bg_img_name,'std_photo'=>$std_img,'status'=>1]
    	);
    	return redirect()->route('certificate.generate')->with('message_success',$request->name.' Saved');
    }
    public function delete(Request $request,$id){
         $data['update']=DB::table('certificates')->where('id',$id)->update(['status'=>0]);
         
         return redirect('certificate/manage')->with('message_warning','Certificate Deleted');
    }
    
}
