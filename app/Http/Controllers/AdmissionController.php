<?php

namespace App\Http\Controllers;
  use App\Http\Controllers\CollegeBaseController;
  use App\category_model;
  use Illuminate\Http\Request;
  use App\Models\StudentStatus;
  use App\Models\Student;

  use App\Models\Faculty;
  use App\Enquiry;
  use App\Branch;
  use App\Admission;
  use App\User;
  use Auth;
  use DB, Session;
  use Carbon\Carbon;

class AdmissionController extends CollegeBaseController
{
    
    protected $base_route = 'admission';
    protected $view_path = 'admission';
    protected $panel = 'Registration';
    protected $folder_path;
    protected $folder_name = 'admissionProfile';
    protected $filter_query = [];
public function index()
    {
    $org_id = Auth::user()->org_id;
    $branch_id = Session::get('activeBranch');
    
    $academic_status_option = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
    $academic_status_option=array_prepend($academic_status_option, 'Select Academic Status', '');

    $branch = Branch::select('branch_name','id')->pluck('branch_name', 'id')->toArray();
    $branch=array_prepend($branch, '----Select Branches----', '');
    
    $category = category_model::select('id', 'category_name')->pluck('category_name','id')->toArray();
    $category=array_prepend($category, '----Select Category----', '');

    $courses = Faculty::select('id', 'faculty')
            ->where('branch_id' , $branch_id) 
            ->orderBy('faculty')
            ->get();
    //$courses = array_prepend($courses, '----Select Course----', '');  
      
      $session_option=DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray();
      $session_option=array_prepend($session_option, '----Select Session----', ''); 

		$pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
      
        $pay_type_list = array_prepend($pay_type_list, "--Payment Mode--", "");
		$religion=DB::table('religions')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $religion=array_prepend($religion,"--Select Religion--",'');
    $handicap=DB::table('handicaps')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $handicap=array_prepend($handicap,"--Select Handicap--",'');
     $references=DB::table('reference')->select('title','id')->where('record_status',1)->pluck('title','id')->toArray();
    $references=array_prepend($references,'--Select Reference--','');
      return view('admission.index', compact('pay_type_list','branch','academic_status_option','courses','category','branch_id','org_id', 'session_option','religion','handicap','references')); 
    }


    public function enquiry($id)
    {
    $enq_id=$id;
    $org_id = Auth::user()->org_id;
    $data = [];
    $key = $id-1;
    //return $key;
    $enquiry = Enquiry::where('id', $id)->get();
   $branch_id=$enquiry[0]->branch_id;
    
      $academic_status_option = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
      $academic_status_option = array_prepend($academic_status_option, '--Select Academic Status--','');

      $branches = Branch:: select('branch_name','branch_address','id','org_id','branch_logo')->where('org_id', $org_id)->get();
     
       $courses = Faculty::select('id', 'faculty')
            ->where('branch_id' , $branch_id) 
            ->orderBy('faculty')
            ->get();

      $session_option=DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray();
      
      $session_option=array_prepend($session_option, '----Select Session----', '');
       $category = category_model::select('id', 'category_name')->pluck('category_name','id')->toArray();
      $category=array_prepend($category, '--Select Category--', '');

      $pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
      $pay_type_list = array_prepend($pay_type_list, "--Payment Mode--", "");
    $religion=DB::table('religions')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $religion=array_prepend($religion,"--Select Religion--",'');
    $handicap=DB::table('handicaps')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $handicap=array_prepend($handicap,"--Select Handicap--",'');
    $references=DB::table('reference')->select('title','id')->where('record_status',1)->pluck('title','id')->toArray();
    $references=array_prepend($references,'--Select Reference--','');

      return view('admission.index', compact('pay_type_list','data','branches','academic_status_option','courses','category', 'branch_id','org_id', 'session_option', 'id', 'enquiry','enq_id','religion','handicap','references'));

    }


    public function store(Request $request)
    {
      $branch_id = Session::get('activeBranch'); //Auth::user()->branch_id;
      $branches = Branch:: select('branch_name','branch_address','branch_email','branch_mobile','branch_logo')->where('id', $branch_id)->get();
      $request->request->add(['org_id'=>Auth::user()->org_id, 'branch_id'=>$branch_id]);

       $request->request->add(['created_by'=>auth()->user()->id]);
      $request->request->add(['updated_by'=>auth()->user()->id]);
      $request->request->add(['session_id'=>Session::get('activeSession')]);
      $admission =Admission::create($request->all());
	    return redirect()->route('.admission_print',$admission->id);
    }

	public function admission_print($id)
	{
		$admId 		= $id;
		$branch_id 	= Session::get('activeBranch'); //Auth::user()->branch_id;
		$branches 	= Branch:: select('branch_name','branch_address','branch_email','branch_mobile','branch_logo')->where('id', $branch_id)->get();
      
		
		$lastrecord = Admission::select('admissions.*', 'faculties.faculty as course' , 'users.name')->leftjoin('faculties', function($join){
          $join->on('faculties.id', '=', 'admissions.course');
      })
    ->leftjoin('users' ,'admissions.created_by' ,'=', 'users.id' )

    ->where('admissions.id', $admId)->first();
	  //dd($lastrecord);
      return view('admission.include.printform', compact('lastrecord','branches'));
	}
	
    public function admissionlist(Request $request)
    {
      $branch_id = Session::get('activeBranch');//Auth::user()->branch_id;
        //return $branch_id;
      $current_session_id=Session::get('activeSession'); 
      $data['faculties'] = $this->activeFaculties();
      $data['category_name'] = category_model::get();
      $academic_status_option = StudentStatus::select('title')->orderBy('title','asc')->Active()->pluck('title','title')
      ->toArray();
      $academic_status_option=array_prepend($academic_status_option, 'Select Head', '');
      //$admission_list = Admission::get()->all();
      //return $admission_list;
        
      //$admission_list = Admission::get()->where('branch_id', $branch_id)->all();
      $data['user']=User::select('users.id',DB::raw("CONCAT(users.name,' ( ',roles.display_name,' )') as name"))
      ->leftjoin('roles','users.role_id','=','roles.id')
        ->where(
          [
            ['users.branch_id','=',Session::get('activeBranch')],
            ['role_id','!=',6],
            ['role_id','!=',7]

        ])->pluck('name','id')->toArray();
        $data['user']=array_prepend($data['user'],'--Admission By--','');
      $admission_list = Admission::select('admissions.*', 'faculties.faculty as course','users.name','std.admission_id as adm_id')
      ->leftJoin('faculties', function($join) {
        $join->on('faculties.id', '=', 'admissions.course');      
      })->where(function ($query) use ($request) {
      
        if($request->name) {
          $query->where('admissions.first_name', 'like', '' . $request->name . '%')
          ->orWhere('admissions.first_name', 'like', '%' . $request->name . '%')
          ->orWhere('admissions.first_name', 'like', '%' . $request->name . '');
          $this->filter_query['name'] = $request->name;
        }

        if ($request->faculty) {
            $query->where('admissions.course', 'like', '%' . $request->faculty . '%');
            $this->filter_query['faculty'] = $request->faculty;
        }

        if ($request->mobile) {
            $query->where('admissions.mobile', $request->mobile)->orWhere('admissions.mobile', 'like', '%'.$request->mobile.'%');
            $this->filter_query['mobile'] = $request->mobile;
        }

        if ($request->category) {
            $query->where('admissions.category_id', $request->category);
            $this->filter_query['category'] = $request->category; 
        }
        if($request->adm_by){
          $query->where('admissions.created_by','=',$request->adm_by);
        }
        if($request->ref_no){
            $query->where('admissions.reference_no', 'like', '%'.$request->ref_no.'%'); 
            $this->filter_query['ref_no'] = $request->ref_no;
          }

         if ($request->head) {
            $query->where('admissions.academic_status', $request->head);
            $this->filter_query['academic_status'] = $request->head; 
        }

        if ($request->reg_start_date && $request->reg_end_date) {
            $query->whereBetween('admissions.admission_date', [$request->get('reg_start_date'), $request->get('reg_end_date')]);
            $this->filter_query['reg-start-date'] = $request->get('reg_start_date');
            $this->filter_query['update-end-date'] = $request->get('reg_end_date');
        }else{

          if(isset($_GET['reg_start_date'])){ 
          }else{
              $start_date=date("Y-m-d");
              $end_date=date("Y-m-d");
              
              $query->whereBetween('admissions.admission_date', [$start_date, $end_date]);
              $this->filter_query['reg-start-date'] = $start_date;
              $this->filter_query['update-end-date'] = $end_date;
            }
          }
      })
      ->leftjoin('users' ,'admissions.created_by' ,'=', 'users.id' )
      ->leftjoin('students as std','admissions.id','=','std.admission_id')
      ->where('admissions.branch_id', $branch_id)
       ->where('admissions.session_id', $current_session_id)
      ->orderBy('admissions.id', 'DESC')->get();
       //return $admission_list;
      
      $data['filter_query'] = $this->filter_query;
      
      //dd("inside==>".$branch_id);
      return view('admission.include.list', compact('admission_list', 'data','academic_status_option','panel'));

      //dd('hello');

    }

    public function admissionedit($id)
    {

    $branch_id  = Session::get('activeBranch'); 
    $org_id = Auth::user()->org_id;
    $data = Branch:: select('branch_name','branch_title','id','org_id')->where('org_id', $org_id)->get();
     $courses = Faculty::select('id', 'faculty')
            ->where('branch_id' , $branch_id) 
            ->orderBy('faculty')
            ->get();

    $academic_status_option = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
    $academic_status_option = array_prepend($academic_status_option, '--Select Academic Status--','');

    $category = category_model::select('id', 'category_name')->pluck('category_name','id')->toArray();
    $category=array_prepend($category, '--Select Category--', '');

    $enquiry = Admission::where('id', $id)->get();
    $branch_id = $enquiry[0]['branch_id'];
    $branch= Branch::where('id', $branch_id)->get();
    //return $enquiry;
    $selected=""; //'admission.edit'

    $session_option=DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray();
    $session_option=array_prepend($session_option, '----Select Session----', '');

	$pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
    $pay_type_list = array_prepend($pay_type_list, "--Payment Mode--", "");
    $religion=DB::table('religions')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $religion=array_prepend($religion,"--Select Religion--",'');
    $handicap=DB::table('handicaps')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $handicap=array_prepend($handicap,"--Select Handicap--",'');
	$references=DB::table('reference')->select('title','id')->where('record_status',1)->pluck('title','id')->toArray();
    $references=array_prepend($references,'--Select Reference--','');
    return view('admission.index', compact('pay_type_list','enquiry', 'academic_status_option','courses','data','branch','category', 'selected', 'branch_id','org_id', 'session_option', 'id','religion','handicap','references'));
  
    }

    public function admissionupdate(Request $request, $id)
    {   
        
        //dd($id);
        $row = Admission::find($id);
        $row->update($request->all());
        // return 'updated';
        // dd();
        //return $row;
        //Session::set('branch', $branch_id);
        //$request['branch_id']=1;
        //$enquiry = Enquiry::create($request->all());
        //return'inserted';
        //return view('enquiry.enquiry');
        return redirect()->route('.admission_list')->with('msg', 'Admission form updated sucessfully');
    }
    
    
    public function GetAge($dob)
    {
      
       
       $age = \Carbon\Carbon::parse($dob)->diff(\Carbon\Carbon::now())->format('%y Years, %m Months and %d Days');
    
       return $age;
    }
   
}
