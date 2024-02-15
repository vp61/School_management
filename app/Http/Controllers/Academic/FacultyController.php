<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Academic\Faculty\AddValidation;
use App\Http\Requests\Academic\Faculty\EditValidation;
use App\Models\Faculty;
use App\Models\Semester;
use App\Fee_model;
use App\AssignFee;
use Session;
use Illuminate\Http\Request;
use Auth,DB;
use Response;
class FacultyController extends CollegeBaseController
{
    protected $base_route = 'faculty';
    protected $view_path = 'academic.faculty';
    protected $panel = 'Faculty/Level/Class';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index()
    {
        //dd();
       $data = [];
       $branch_id = Session::get('activeBranch'); //Auth::user()->branch_id;
       $org_id = Auth::user()->org_id;
       $data['faculty'] = Faculty::select('*')
       ->where('org_id' , $org_id)
       ->where('branch_id' , $branch_id)
        ->orderBy('faculty')
        ->get();

       $data['semester'] = Semester::select('id', 'semester')
            ->Active()
            ->get();
        $data['courseType']=DB::table('course_type')->select('id','title')->where([
            ['branch_id','=',Session::get('activeBranch')],
            // ['session_id','=',Session::get('activeSession')],
            ['status','=',1]
        ])->pluck('title','id')->toArray();
        $data['courseType']=array_prepend($data['courseType'],'--Select '.env("course_label").' Type--',''); 

       return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(AddValidation $request)
    {
     // return $request;
     // dd();
        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['slug' => $request->get('faculty')]);

        $faculty = Faculty::create($request->all());

        $semesters = [];
        if($request->get('semester')){
            foreach ($request->get('semester') as $semester){
                $semesters[$semester] = [
                    'faculty_id' => $faculty->id,
                    'semester_id' => $semester
                ];
            }
        }

        $faculty->semester()->sync($semesters);

        $request->session()->flash($this->message_success, $this->panel. 'Created Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit($id)
    {

        $data = [];
        if (!$data['row'] = Faculty::find($id))
            return parent::invalidRequest();

        $data['faculty'] = Faculty::select('id', 'faculty', 'status')->where('branch_id',Session::get('activeBranch'))
            ->orderBy('faculty')
            ->get();

        $data['semester'] = Semester::select('id', 'semester')
            ->Active()
            ->get();

        $data['active_semester'] = $data['row']->semester()->pluck('semesters.semester', 'semesters.id')->toArray();
        $data['courseType']=DB::table('course_type')->select('id','title')->where([
            ['branch_id','=',Session::get('activeBranch')],
            // ['session_id','=',Session::get('activeSession')],
            ['status','=',1]
        ])->pluck('title','id')->toArray();
        $data['courseType']=array_prepend($data['courseType'],'--Select Course Type--',''); 
        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        if (!$row = Faculty::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['slug' => $request->get('faculty')]);

        $faculty = $row->update($request->all());

        if($faculty){
            $semesters = [];
            if($request->get('semester')){
                foreach ($request->get('semester') as $semester){
                    $semesters[$semester] = [
                        'faculty_id' => $row->id,
                        'semester_id' => $semester
                    ];
                }
            }

            $row->semester()->sync($semesters);
        }


        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        if (!$row = Faculty::find($id)) return parent::invalidRequest();

        $row->delete();
        $row->semester()->detach();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $row = Faculty::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Faculty::find($row_id);
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $request->get('bulk_action'). ' Action Successfully.');
                else
                    $request->session()->flash($this->message_success, 'Deleted successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, Check at least one row.');
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();

    }

    public function active(request $request, $id)
    {
        if (!$row = Faculty::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        if (!$row = Faculty::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }


     public function getcourseapi($cid)
    { //dd('hello');
       $data = [];
       $data = Faculty::select('id', 'faculty', 'status')
       ->where('branch_id' , $cid)
        ->orderBy('faculty')
        ->get();


      

       if (! $data) {

     return Response::json( [
            'error' =>[
                'messege' => 'Posts does not exists',
                'errorCode' =>'404'
            ],  
            'data' =>[
                
            ]


            ], 404);
        }

         return Response::json([
             'error' =>[
                'null'
            ],
             'data' =>
                 $data
            


            ],200);

        
   }

    public function feebycourseapi($cid,$sessionId="")
    { 
    $assign_list=Fee_model::select('assign_fee.*', 'fee_heads.fee_head_title', 'session.session_name')
    ->leftJoin('fee_heads', function($join){
                $join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
        })
    ->leftJoin('session', function($join){
            $join->on('session.id', '=', 'assign_fee.session_id');
        }) 
        ->where('course_id' , $cid)
        ->where(function($q) use ($sessionId){
            if($sessionId!=""){
                $q->where('assign_fee.session_id',$sessionId);
            }
        })
       ->where('student_id', '0')
       ->where('assign_fee.status', '1')
        ->get();
       
       if (! $assign_list) {

     return Response::json( [
            'error' =>[
                'messege' => 'Posts does not exists',
                'errorCode' =>'404'
            ],  
            'data' =>[
                
            ]


            ], 404);
        }

         return Response::json([
             'error' =>[
                'null'
            ],
             'data' =>
                 $assign_list
            


            ],200);

        
   }


    public function getcoursebycollege()
    { 
        
    $cid = 1;//Session::get('activeBranch');
    //dd($cid);

        
    $data = [];
       $data = Faculty::select('id', 'faculty', 'status','form_fees')
       ->where('branch_id' , $cid)
        ->orderBy('faculty')
        ->get();

      

       if (! $data) {

     return Response::json( [
            'error' =>[
                'messege' => 'Posts does not exists',
                'errorCode' =>'404'
            ],  
            'data' =>[
                
            ]


            ], 404);
        }

         return Response::json([
             'error' =>[
                'null'
            ],
             'data' =>
                 $data
            


            ],200);
}


  public function getfeebycourse($id)
    { 
    
        
    $data = [];
       $data = Faculty::select( 'form_fees')
       ->where('id' , $id)
        ->get();

      

       if (! $data) {

     return Response::json( [
            'error' =>[
                'messege' => 'Posts does not exists',
                'errorCode' =>'404'
            ],  
            'data' =>[
                
            ]


            ], 404);
        }

         return Response::json([
             'error' =>[
                'null'
            ],
             'data' =>
                 $data
            


            ],200);
}


   
}
