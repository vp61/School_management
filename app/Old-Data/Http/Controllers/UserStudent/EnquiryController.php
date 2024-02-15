<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CollegeBaseController;
 //use \App\Http\Controllers\UserStudent\Student;
 use App\Models\Student;
 use App\Enquiry;


class EnquiryController extends CollegeBaseController
{

	protected $base_route = 'enquiry';
    protected $view_path = 'enquiry';

    public function index(Request $request)
    {
   $data = [];
        $data['student'] = Student::select('students.id', 'students.reg_no', 'students.reg_date',
            'students.faculty', 'semester', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'ai.mobile_1', 'students.status')
            ->where(function ($query) use ($request) {

                if ($request->has('reg_no')) {
                    $query->where('students.reg_no', 'like', '%' . $request->reg_no . '%');
                    $this->filter_query['students.reg_no'] = $request->reg_no;
                }

                if ($request->has('reg-start-date') && $request->has('update-end-date')) {
                    $query->whereBetween('students.reg_date', [$request->get('reg-start-date'), $request->get('update-end-date')]);
                    $this->filter_query['reg-start-date'] = $request->get('reg-start-date');
                    $this->filter_query['update-end-date'] = $request->get('update-end-date');
                } elseif ($request->has('reg-start-date')) {
                    $query->where('students.reg_date', '>=', $request->get('reg-start-date'));
                    $this->filter_query['reg-start-date'] = $request->get('reg-start-date');
                } elseif ($request->has('reg-end-date')) {
                    $query->where('students.reg_date', '<=', $request->get('reg-end-date'));
                    $this->filter_query['reg-end-date'] = $request->get('reg-end-date');
                }

                if ($request->has('faculty')) {
                    $query->where('students.faculty', 'like', '%' . $request->faculty . '%');
                    $this->filter_query['students.faculty'] = $request->faculty;
                }

                if ($request->has('semester')) {
                    $query->where('students.semester', 'like', '%' . $request->semester . '%');
                    $this->filter_query['students.semester'] = $request->semester;
                }

                if ($request->has('status')) {
                    $query->where('students.status', $request->status == 'active' ? 1 : 0);
                    $this->filter_query['students.status'] = $request->get('status');
                }

            })
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->get();

        $data['faculties'] = $this->activeFaculties();

        // $data['url'] = URL::current();
        // $data['filter_query'] = $this->filter_query;
       //return $data;
    	return view('enquiry.enquiry', compact('data'));
 
    }



    public function store(request  $request)
    {
         
         $request['branch_id']=1;
         $enquiry = Enquiry::create($request);
         return'inserted';
         return view('enquiry.enquiry');

    }
}
