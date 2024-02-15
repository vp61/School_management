<?php

namespace App\Http\Controllers\Certificate;

use Illuminate\Http\Request;
use App\Http\Controllers\CollegeBaseController;
use App\Models\Faculty;
use Session,DB,URL;
use Carbon\Carbon;

class CertificateController extends CollegeBaseController
{
    protected $base_route = 'certificate';
    protected $view_path = 'certificate';
    protected $panel = 'Certificate';
    protected $filter_query = [];

    public function __construct()
    {

    }
    public function index(Request $request){
    	$data['course']=Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->orderBy('faculty','ASC')->pluck('faculty','id')->toArray();
        $data['course']=array_prepend($data['course'],"--Select Course--","");  
        $data['section']=Faculty::select('semesters.id','semesters.semester')
        ->where('faculties.branch_id',Session::get('activeBranch'))
        ->distinct('semesters.id')
        ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
        ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
        ->orderBy('semester','ASC')
        ->pluck('semesters.semester','semesters.id')
        ->toArray();
        $data['section']=array_prepend($data['section'],"--Select Section--","");
        $data['certificate']=DB::table('certificates')->select('id','title')->where('status',1)->pluck('title','id')->toArray();
        $data['certificate']=array_prepend($data['certificate'],'--Select Certificate--','');

    	return view(parent::loadDataToView($this->view_path.'.generate.index'),compact('data'));
    }
    public function generate_certificate(Request $request){
        
            $headerimg=asset('certificate'.DIRECTORY_SEPARATOR);
            $headerimg=addslashes($headerimg).'/';
            $bgimg=asset('certificate');
            $bgimg=addslashes($bgimg).'/';
       

    	$data['certificate']=DB::table('certificates')->select('*',DB::raw("CONCAT('$headerimg',header_img) as header"),DB::raw("CONCAT('$bgimg',bg_img) as bg_image"))->where('id',$request->certificate)->first();
        $stdimg=asset('images'.DIRECTORY_SEPARATOR.'studentProfile');
        $stdimg=addslashes($stdimg).'/';
        
    	$data['student']=DB::table('student_detail_sessionwise')->select('st.first_name as st_name','st.date_of_birth as dob','st.reg_no','st.reg_date','st.email','st.gender','st.student_image','add.address','add.mobile_1 as mobile','parent.father_first_name as father_name','parent.mother_first_name as mother_name',DB::raw("CONCAT('$stdimg',student_image) as image"))
    	->where('st.id',$request->student)
    	->leftjoin('students as st','st.id','=','student_detail_sessionwise.student_id')
    	->leftjoin('parent_details as parent','parent.students_id','=','st.id')
    	->leftjoin('addressinfos as add','add.students_id','=','st.id')
    	->first();

        $date=date('d-m-Y', strtotime($data['student']->reg_date));
    	$body=$data['certificate']->body;
    	$body=str_replace('|name|',$data['student']->st_name , $body);
    	$body=str_replace('|dob|', $data['student']->dob, $body);
		$body=str_replace('|father_name|', $data['student']->father_name, $body);
		$body=str_replace('|mother_name|', $data['student']->mother_name, $body);
		$body=str_replace('|address|', $data['student']->address, $body);
		$body=str_replace('|mobile|',$data['student']->mobile , $body);
		$body=str_replace('|email|', $data['student']->email, $body);
		$body=str_replace('|reg_no|', $data['student']->reg_no, $body);
		$body=str_replace('|reg_date|', $date , $body);
		$body=str_replace('|class|', 'abc', $body);
		$body=str_replace('|section|', 'abc', $body);
		$body=str_replace('|gender|', $data['student']->gender, $body);
        $data['body']=$body; 

        return view(parent::loadDataToView($this->view_path.'.generate.certificate'),compact('data')); 	
    	
    }
}
