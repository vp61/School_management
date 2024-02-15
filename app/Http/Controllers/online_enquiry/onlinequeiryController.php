<?php

namespace App\Http\Controllers\online_enquiry;

use Illuminate\Http\Request;
use App\Http\Controllers\CollegeBaseController;
use App\Branch;
use DB,Log,Session,File;
use Carbon\Carbon;
use Tzsk\Payu\Facade\Payment;
use App\Models\GeneralSetting;
use App\Models\Student;
use App\Enquiry;

use App\Models\Faculty;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\ImageManagerStatic as Image;


class onlinequeiryController extends CollegeBaseController
{
    //
    protected $base_route = 'online_enquiry';
    protected $view_path = 'online_enquiry';
    protected $panel = ' Online enquiry Form';
    protected $filter_query = [];
    protected $branch_id = 1;

    public function __construct(){
        
    }
    public function index(Request $request){
        $branch_id = $this->branch_id;
    	$data['branch']= DB::table('branches')->select('*')->where('id',1)->where('record_status',1)->first();
        $data['image']=asset('assets/images/marine_bg/marine_college_bg.jpg');

        $courses = Faculty::select('id', 'faculty')
        ->where('branch_id' , $branch_id)
        ->where('show_on_online_froms' , 1)
        ->orderBy('id')
        ->orderBy('faculty')
        ->pluck('faculty', 'id')
        ->toArray();
        $courses = array_prepend($courses,'Select '.env("course_label"),"");      
        $data['type'] =Crypt::encrypt($request->type);
        $year_list = range(2000,2021);
            foreach($year_list as $k => $v){
                $data['year_list'][$v]= $v;
            }
            $data['year_list'] = array_prepend($data['year_list'],'--Select Year--','');

    	return view($this->view_path.'.home',compact('data','courses'));

    }
    public function store(Request $request){
       // dd($request->all());
        $date= Carbon::now();
        $rules=[
            'first_name'=>'required',
            'father_name'=>'required',
            'mother_name'=>'required',
            'course'=>'required',
            'gender'=>'required',
            'date_of_birth'=>'required',
            'mobile'=>'required|digits:10',
            'address'=>'required',
            'state'=>'required',
            'city'=>'required',
            
        ];
        $msg=[
            'first_name.required'=>"Please Enter Student's Name",
            'father_name.required'=>"Please Enter Father's Name",
            'mother_name.required'=>"Please Enter Mother's Name",
            'course.required'=>'Please Enter '.env('course_label'),  
            'gender.required'=>'Please Select Gender ',
            'date_of_birth.required'=>'Please Enter Date of birth',
            'mobile.required'=>'Please Enter Mobile Number',
            'address.required'=>'Please Enter Address',
            'state.required'=>'Please Enter State',
            'city.required'=>'Please Enter City',
            
        ];
        $this->validate($request,$rules,$msg);
        
        $active_session = 0;
        
        $session = DB::table('session')->select('id')->where('active_status',1)->orderBy('id','desc')->first();
        if($session){
            $active_session = $session->id;
        }
        
        $request->request->remove('_token');
        $request->request->add(['created_at' => $date]);
        $request->request->add(['enq_date' => $date]);
        $request->request->add(['branch_id'=>$this->branch_id]);
        $request->request->add(['session_id'=>$active_session]);
        $request->request->add(['created_by'=>0]);
        $request->request->add(['org_id'=>1]);
        

        Enquiry::create($request->all());

        $request->session()->flash($this->message_success,'Thanks for your enquiry, We will contact you shortly.');

        return redirect()->route($this->base_route);
    }
}
