<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::middleware('api')->get('/getcourseby{cid}', ['as' => '.getcourse',            'uses' => 'FacultyController@getcourse']);


Route::group(['middleware' => 'api'], function(){
	Route::get('/getcourseby/{cid}', 'Academic\FacultyController@getcourseapi');
	

	
	
	// Route::get('/posts/{id}', 'PostController@apishow');
	// Route::get('/categories', 'CategoryController@Apindex');
	// Route::get('/categories/{id}', 'CategoryController@apishow');
});
/*ZOOM MEETINGS*/
Route::group(['middleware' => 'api'], function(){
  Route::post('/create-zoom-meeting','VideoConference\ZoomMeetingController@index');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/feebycourse/{id}', 'Academic\FacultyController@feebycourseapi');
	Route::get('/feebycourse/{id}/{session}', 'Academic\FacultyController@feebycourseapi');

});

Route::group(['middleware' => 'api'], function(){
	
	Route::get('/getcourse', 'Academic\FacultyController@getcoursebycollege');
	

});

Route::group(['middleware' => 'api'], function(){
	
	Route::get('/getfeebycourse/{id}', 'Academic\FacultyController@getfeebycourse');

});

// ========== [Start] Student Dashboard / Profile / Fee  ========
Route::group(['middleware' => 'api'], function(){
	Route::post('/student_login', 'Api\ServicesController@asha_login');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/student_dashboard_info', 'Api\ServicesController@get_dash_board_info');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/student_profile', 'Api\ServicesController@get_student_profile');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/student_academic_info', 'Api\ServicesController@get_student_academic');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/student_note_info', 'Api\ServicesController@get_student_note');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/student_document_info', 'Api\ServicesController@get_student_document');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/student_session_info', 'Api\ServicesController@get_student_session_list');
});

// [FEE: START]
Route::group(['middleware' => 'api'], function(){
	Route::get('/student_fee_head_info', 'Api\ServicesController@get_student_fee_head_list');
});


Route::group(['middleware' => 'api'], function(){
	Route::get('/student_receipt_list', 'Api\ServicesController@get_student_fee_receipt_list');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/get_check_sum_hash', 'Api\ServicesController@getHashFromVal');
});

Route::group(['middleware' => 'api'], function(){
	Route::post('/get_check_sum_hash', 'Api\ServicesController@getHashFromVal');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/get_reverse_check_sum_hash', 'Api\ServicesController@checkRevershashVal');
});

Route::group(['middleware' => 'api'], function(){
	Route::post('/get_reverse_check_sum_hash', 'Api\ServicesController@checkRevershashVal');
});
// [FEE: END]

Route::group(['middleware' => 'api'], function(){
	Route::get('/student_branch_info', 'Api\ServicesController@getBranchInformation');
});

// ========== [End]Student Dashboard / Profile / Fee ========

Route::group(['middleware' => 'api'], function(){
	Route::get('/getfeebycourselist/{id}', 'Api\ServicesController@getfeebycourse');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/getcourselist', 'Api\ServicesController@getcoursebycollege');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/feebycourselist/{id}', 'Api\ServicesController@feebycourseapi');
});


/************** [Start]FEE RECEIPT *************/
Route::group(['middleware' => 'api'], function(){
	Route::post('/process_fee_payment', 'Api\ServicesController@fee_payment_process');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/process_fee_payment', 'Api\ServicesController@fee_payment_process');
});


Route::group(['middleware' => 'api'], function(){
	Route::post('/save_online_fee_payment', 'Api\ServicesController@fee_payment_online_save');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/save_online_fee_payment', 'Api\ServicesController@fee_payment_online_save');
});


/************** [End]FEE RECEIPT *************/

Route::group(['middleware' => 'api'], function(){
	Route::get('/staffdetails/{staff_id}', 'Api\ServicesController@get_Staff_Details');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/staffdetails', 'Api\ServicesController@get_Staff_Details');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/staffattendance/{staff_id}/{year_id}/{month_id}', 'Api\ServicesController@get_Staff_Attendance');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/staffattendance/{staff_id}/{year_id}', 'Api\ServicesController@get_Staff_Attendance');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/student_attendance/{std_id}/{year_id}/{month_id}', 'Api\ServicesController@get_Student_Attendance_Status');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/student_attendance/{std_id}/{year_id}', 'Api\ServicesController@get_Student_Attendance_Status');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/staffdocument/{staff_id}', 'Api\ServicesController@get_Staff_Document');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/staffdocument', 'Api\ServicesController@get_Staff_Document');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/years', 'Api\ServicesController@get_Year_List');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/months', 'Api\ServicesController@get_Month_List');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/attendancetitle', 'Api\ServicesController@get_Attendance_Title');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/staffnotes/{staff_id}', 'Api\ServicesController@get_Staff_Note');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/staffnotes', 'Api\ServicesController@get_Staff_Note');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/staffpayroll/{staff_id}', 'Api\ServicesController@get_Staff_Payroll');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/staffpayroll', 'Api\ServicesController@get_Staff_Payroll');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('/addattendance', 'Api\ServicesController@update_student_attendance');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('/assignmentlist', 'Api\ServicesController@get_assignment_list');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('/addassignment', 'Api\ServicesController@add_assignment');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('/addassignmentfile', 'Api\ServicesController@add_assignment_file');
});
Route::group(['middleware' => 'api'], function(){
    Route::get('/transportdetails/{memId}/{memType}', 'Api\ServicesController@get_transport_details');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('/assignmentsubmit', 'Api\ServicesController@assignment_submitted');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('/assignmentcomment', 'Api\ServicesController@post_Assignment_comment_student');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('/submittedAssignmentList', 'Api\ServicesController@post_Assignment_submit');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('/viewAssignmentAnswer', 'Api\ServicesController@view_assignment_answer');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('/assignmentstatus', 'Api\ServicesController@post_assignment_status');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/branches_list', 'Api\ServicesController@get_branch_list');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/faculty_courses_list', 'Api\ServicesController@get_faculty_course_list');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/faculty_courses_list/{branch_id}', 'Api\ServicesController@get_faculty_course_list');
});

Route::group(['middleware' => 'api'], function(){
	Route::get('/session_batch_list', 'Api\ServicesController@get_session_batch_list');
});
Route::group(['middleware' => 'api'], function(){
    Route::get('/attendancelist/{sessionId}/{branchId}/{courseId}/{sectionId}', 'Api\ServicesController@get_student_attendance');
});
Route::group(['middleware' => 'api'], function(){
    Route::get('/getBranchCourse/{branch_id}', 'Api\ServicesController@get_course_branch');
});
Route::group(['middleware' => 'api'], function(){
    Route::get('/getSectionByCourse/{course_id}', 'Api\ServicesController@get_section_by_course');
});
 Route::group(['middleware' => 'api'], function(){
    Route::post('timetable/checkTeacher', 'Api\ServicesController@checkTeacher');
});
 
 Route::group(['middleware' => 'api'], function(){
    Route::post('timetable/loadSchedule', 'Api\ServicesController@loadSchedule');
});
 Route::group(['middleware' => 'api'], function(){
    Route::post('timetable/loadSection', 'Api\ServicesController@loadSection');
});
 Route::group(['middleware' => 'api'], function(){
    Route::post('timetable/delete', 'Api\ServicesController@deleteSchedule');
});
 Route::group(['middleware' => 'api'], function(){
    Route::post('timetable/alternateTeacher', 'Api\ServicesController@autoAssign');
}); 
 Route::group(['middleware' => 'api'], function(){
    Route::post('studentTimetable', 'Api\ServicesController@studentTimetable');
});
Route::group(['middleware' => 'api'], function(){
    Route::post('studentDailyTimetable', 'Api\ServicesController@postStudentDailyTimetable');
});
Route::group(['middleware' => 'api'], function(){
	Route::get('/get_key_salt/{key_id}', 'Api\ServicesController@get_key_salt_by_key');
});
Route::group(['middleware' => 'api'], function(){
	Route::post('/create_zoom_meeting', 'Api\ServicesController@create_zoom_meeting');
});
Route::group(['middleware' => 'api'], function(){
	Route::post('/zoom_class_list_staff', 'Api\ServicesController@zoom_class_list_staff');
});
Route::group(['middleware' => 'api'], function(){
	Route::post('/zoom_class_list_student', 'Api\ServicesController@zoom_class_list_student');
});

/*NEW COPIED FROM SHEAT */
    Route::group(['middleware' => 'api'], function(){
        Route::get('get_notice_count', 'Api\ServicesController@getNoticeCount');
    });
     Route::group(['middleware' => 'api'], function(){
        Route::post('weeklyTimetable', 'Api\ServicesController@postWeeklyTimetable');
    });
    Route::group(['middleware' => 'api'], function(){
        Route::get('hostelResident/{branchId}/{sessionId}/{memberId}/{memberType}', 'Api\ServicesController@getHostelResident');
    });
    Route::group(['middleware' => 'api'], function(){
        Route::post('/add_assignment_answer', 'Api\ServicesController@post_student_assignment_answer');
    });
    Route::group(['middleware' => 'api'], function(){
        Route::get('/notice/{role_id}', 'Api\ServicesController@getNotice');
    });
    Route::group(['middleware' => 'api'], function(){
        Route::get('/getTeacher', 'Api\ServicesController@getTeacher');
    });
    Route::group(['middleware' => 'api'], function(){
        Route::post('/examResult', 'Api\ServicesController@postExamResult');
    });
    Route::group(['middleware' => 'api'], function(){
        Route::post('/studentExam', 'Api\ServicesController@postStudentExam');
    });
/*NEW COPIED FROM SHEAT END */
Route::group(['middleware' => 'api'], function(){
    
    /* LONGITUDE LATITUDE FOR ATTENDANCE */
    Route::post('/get_attendance_coordinates', 'Api\ServicesController@attendance_coordinates');
    
	/* LIVE CLASS & INTERNAL MEETING */
	Route::post('/live_class_attendance', 'Api\ServicesController@live_class_attendance');
	Route::post('/live_meeting_log', 'Api\ServicesController@live_meeting_log');
	Route::post('/meeting_attendance_list', 'Api\ServicesController@list_attendance');
	Route::get('/delete_live_class/{meeting_id}/{user_id}', 'Api\ServicesController@delete_live_class');
	Route::get('/meeting_joiner_type', 'Api\ServicesController@meeting_joiner_type');
	Route::post('/create_internal_meeting', 'Api\ServicesController@create_internal_meeting');
	Route::post('/internal_meeting_list_staff', 'Api\ServicesController@internal_meeting_list_staff');
	Route::post('/internal_meeting_list_student', 'Api\ServicesController@meeting_list_student');
	Route::get('/delete_meeting/{meeting_id}/{user_id}', 'Api\ServicesController@delete_meeting');


	Route::get('/live_class_duration', 'Api\ServicesController@liveClassDuration');
	// Route::get('/get_ip', 'Api\ServicesController@getIp');
	/* LIVE CLASS & INTERNAL MEETING END */

	/* EXAM APIS */
	Route::post('/assessment', 'Api\ServicesController@save_assessment');
	Route::get('/exam_mode', 'Api\ServicesController@get_exam_mode');
	Route::get('/exam_term', 'Api\ServicesController@get_exam_term');
	Route::get('/exam_paper', 'Api\ServicesController@get_exam_paper');
	Route::get('/exam_type', 'Api\ServicesController@get_exam_type');
	
	
	Route::get('/exam_question_type', 'Api\ServicesController@get_exam_question_type');
	Route::post('/exam_question_answer', 'Api\ServicesController@post_exam_question_answer');
	// Route::get('/exam_question_answer', 'Api\ServicesController@post_exam_question_answer');
	Route::get('/exam_by_term', 'Api\ServicesController@get_exam_by_term');
	Route::post('/student_by_exam', 'Api\ServicesController@get_student_by_exam');
	Route::get('/show_exam_result', 'Api\ServicesController@show_exam_result');
	Route::get('/getSubjectByCourse', 'Api\ServicesController@getSubjectByCourse');

	Route::post('/save_exam', 'Api\ServicesController@save_exam');
	Route::get('/exam', 'Api\ServicesController@get_exam');
	Route::get('/delete_exam', 'Api\ServicesController@delete_exam');
	Route::get('/change_exam_publish_status', 'Api\ServicesController@exam_publish_status');
	Route::get('/change_exam_result_status', 'Api\ServicesController@exam_result_status');
	Route::get('/exam_wise_student', 'Api\ServicesController@examwiseStudent');

	
	Route::post('/save_exam_question', 'Api\ServicesController@save_exam_question');
	Route::get('/exam_question', 'Api\ServicesController@get_exam_question');
	Route::get('/delete_exam_question', 'Api\ServicesController@delete_exam_question');
	

	Route::post('/questionwise_student_answer', 'Api\ServicesController@questionwiseStudentAnswer');
	// Route::get('/questionwise_student_answer', 'Api\ServicesController@questionwiseStudentAnswer');
	Route::post('/student_exam_mark', 'Api\ServicesController@post_student_exam_mark');

	/*student list for given exam */
	Route::post('/exam_student_list', 'Api\ServicesController@examStudentList');
	Route::post('/student_exam_appearing_status', 'Api\ServicesController@studentAppearingStatus');
	Route::get('/student_exam_appearing_status', 'Api\ServicesController@studentAppearingStatus');
	Route::post('/student_assignment_submission_status', 'Api\ServicesController@studentAssignmentSubmissionStatus');
	
	Route::post('/exam_image_upload', 'Api\ServicesController@examImageBase64Upload');
	
	
	/* EXAM APIS END */

	Route::post('/change_password', 'Api\ServicesController@change_password');
	Route::post('/user_status', 'Api\ServicesController@user_status');

	Route::get('/staff_details', 'Api\ServicesController@staff_details');
	
	
	Route::post('/razorpay_before_send', 'Api\ServicesController@razorpay_before_send');
	Route::post('/razorpay_response', 'Api\ServicesController@razorpay_response');
	
	Route::get('/payment_gateway_credentials', 'Api\ServicesController@payment_gateway_credentials');
	Route::get('/support', 'Api\ServicesController@support');
	
	Route::post('/razorpay_response_demo', 'Api\ServicesController@razorpay_response_demo');
	
	Route::get('/student_assign_fee', 'Api\ServicesController@student_assign_fee');
	
	Route::get('/receiptdetail', 'Api\ServicesController@GetReceiptByNo');
	
	
	Route::post('/student_assignment_detail', 'Api\ServicesController@student_assignment_detail');
	
	Route::post('/change_assignment_answer_status','Api\ServicesController@change_assignment_answer_status');
	Route::post('/delete_assignment','Api\ServicesController@delete_assignment');
	
	Route::get('/student_dashboard_count','Api\ServicesController@student_dashboard_count');
	Route::get('/staff_dashboard_count','Api\ServicesController@staff_dashboard_count');
	Route::post('/upload_staff_profile_image','Api\ServicesController@upload_staff_profile_image');
	
	
	/* Offline Exam Assessment */
	Route::post('/offline_exam_students_list','Api\ServicesController@offline_exam_students_list');
	Route::get('/exam_attendance_status_list','Api\ServicesController@exam_attendance_status_list');
	Route::post('/save_offline_exam','Api\ServicesController@save_offline_exam');
	Route::post('/student_result_link','Api\ServicesController@student_result_link');
    Route::get('/show_result_to_student','Api\ServicesController@show_result_to_student');
	
	/* Offline Exam Assessment End*/
	

    });
    Route::group(['middleware' => 'api'], function(){
    Route::get('/CourseSubjectList', 'Api\ServicesController@CourseSubjectList');

    Route::post('/ChangePassowrd','Api\ServicesController@UpdatePassword');
    Route::post('/get_student_attendance_new', 'Api\ServicesController@get_student_attendance_new');
 
    Route::get('/attendance_status_list','Api\ServicesController@attendance_status_list');
    Route::post('/addattendance_Save', 'Api\ServicesController@save_student_attendance');
 
 /* Firebase Routes */
	Route::post('/store_firebase_token','Api\ServicesController@store_firebase_token');
	Route::post('/update_firebase_login_status','Api\ServicesController@update_firebase_login_status');

	/* Firebase Routes End*/
	
	
	Route::get('/Staff_designation_List', 'Api\ServicesController@Staff_designation_List');
    Route::post('/get_staff_attendance_list', 'Api\ServicesController@get_staff_attendance_list');
    Route::post('/save_staff_attendance', 'Api\ServicesController@save_staff_attendance');
    

    Route::post('/notification_to_students_by_staff','Api\ServicesController@notification_to_students_by_staff');

    Route::post('/notification_to_students_by_principal','Api\ServicesController@notification_to_students_by_principal');
    Route::post('/notification_to_staff','Api\ServicesController@notification_to_staff');
    
    Route::get('/get_firebase_notification_list','Api\ServicesController@get_firebase_notification_list');
    Route::get('/get_firebase_notification_count','Api\ServicesController@get_firebase_notification_count');
    Route::get('/get_student_list','Api\ServicesController@get_student_list');
    
    
    Route::get('/CategoryList','Api\ServicesController@Category');
    Route::get('/ReligionList','Api\ServicesController@Religion');
	Route::post('/UpdateStudentProfile','Api\ServicesController@UpdateStudentProfile');
	Route::get('/history_student_detail','Api\ServicesController@history_student_detail');
	Route::post('/Approve_Update_Profile_Status','Api\ServicesController@approve_update_profile_status');
	Route::get('/history_student_detail_staff_end','Api\ServicesController@history_student_detail_staff_end');
	Route::post('/update_profile_status_check','Api\ServicesController@update_profile_status_check');
	Route::get('/student_profile_show','Api\ServicesController@student_profile_show');
	Route::get('/student_list_profile','Api\ServicesController@student_list_profile');
	Route::get('/student_profiless', 'Api\ServicesController@get_student_profiles');
	
    });

    Route::get('/student_result','Api\ServicesController@student_result');


    
      
    //Admin Api
    
      Route::get('/CategoryList','Api\ServicesController@Category');
    Route::get('/ReligionList','Api\ServicesController@Religion');
	Route::post('/UpdateStudentProfile','Api\ServicesController@UpdateStudentProfile');
	Route::get('/history_student_detail','Api\ServicesController@history_student_detail');
	Route::post('/Approve_Update_Profile_Status','Api\ServicesController@approve_update_profile_status');
	Route::get('/history_student_detail_staff_end','Api\ServicesController@history_student_detail_staff_end');
	Route::post('/update_profile_status_check','Api\ServicesController@update_profile_status_check');
	Route::get('/student_profile_show','Api\ServicesController@student_profile_show');
	Route::get('/student_list_profile','Api\ServicesController@student_list_profile');
//	Route::get('/student_profiless', 'Api\ServicesController@get_student_profiles');
    Route::get('/transport_collection_report','Api\ServicesController@transport_collection_report');
    Route::get('/transport_user_history','Api\ServicesController@transport_user_history');

   	Route::get('/staff_list','Api\ServicesController@staff_list');
    Route::post('/vehicle_add','Api\ServicesController@vehicle_add');
    Route::get('/transport_detail','Api\ServicesController@transport_detail');
    Route::post('/transport_detail_leave','Api\ServicesController@transport_detail_leave');
    Route::get('/vehicle_route_list','Api\ServicesController@vehicle_route_list');
    Route::post('/transport_user_shift','Api\ServicesController@transport_user_shift');

    Route::post('/transport_assign_user','Api\ServicesController@transport_assign_user');

    Route::get('/transport_collection_due_reoprt','Api\ServicesController@transport_collection_due_reoprt');
    Route::post('/delete_vehicle','Api\ServicesController@delete_vehicle');
    Route::post('/update_vehicle_status','Api\ServicesController@update_vehicle_status');
    Route::get('/user_type','Api\ServicesController@user_type');
    Route::get('/status_list','Api\ServicesController@status_list');
    Route::get('/transport_duration','Api\ServicesController@transport_duration');
    Route::get('/payment_mode','Api\ServicesController@payment_mode');
     Route::get('/switch_branch','Api\ServicesController@switch_branch');
    Route::get('/switch_branch_list','Api\ServicesController@switch_branch_list');
    Route::get('/get_all_branch','Api\ServicesController@get_all_branch');
    	Route::get('/allenquiry', 'Api\DashboardController@enquiryList');
    Route::get('/admission_form_sale', 'Api\DashboardController@admission_form_sale');
    Route::get('/all_student', 'Api\DashboardController@Total_Students');
    Route::get('/fee_collect_month_wise', 'Api\DashboardController@Month_wise_fee');
    Route::get('/fee_collect_branch_wise', 'Api\DashboardController@Branch_wise_fee');
    Route::get('/Due_fee_branch_wise', 'Api\DashboardController@Due_Course_wise_fee');
    Route::get('/enquiry_list_search_condition', 'Api\DashboardController@EnuiryListWithCondition');
    Route::get('/student_list_search_condition', 'Api\DashboardController@StudentListWithCondition');
    
    Route::get('/studentcount', 'Api\DashboardController@StudentCount');
    Route::get('/duefees', 'Api\DashboardController@DuefeeColection');
    Route::get('/totalfeecollection', 'Api\DashboardController@Totalfeecollect');
    
    Route::get('/staffcount', 'Api\DashboardController@StaffCount');
    Route::get('/HostelBedCount', 'Api\DashboardController@HostelBedCount');
    Route::get('/VehicleCount', 'Api\DashboardController@VehicleCount');
    Route::get('/ExamCount', 'Api\DashboardController@ExamCount');
    Route::get('/BookCount', 'Api\DashboardController@BookCount');
    
    
    
    Route::get('/CallLog', 'Api\DashboardController@CallLogList');
     Route::get('/ComplaintList', 'Api\DashboardController@ComplaintList');
     Route::get('/PostalList', 'Api\DashboardController@PostalList');
     Route::get('/EnquiryList', 'Api\DashboardController@EnquiryListing');
     Route::get('/VisitorBook', 'Api\DashboardController@VisitorBook');
     Route::get('/ProspectusList', 'Api\DashboardController@ProspectusList');
     Route::get('/BranchfeecollectionMonthwise', 'Api\DashboardController@BranchFeeCollectMonthWise');
      Route::get('/HostelDetailList', 'Api\DashboardController@HostelDetailList');
      Route::get('/TransportDetailList', 'Api\DashboardController@TransportDetailList');
      Route::get('/booksList', 'Api\DashboardController@BooksList');
      
      /*invetory apis*/
      Route::get('/supplierList', 'Api\DashboardController@SupplierList');
      Route::get('/productList', 'Api\DashboardController@ProductList');
      /*attendance apis*/
      Route::get('/studentAttendance', 'Api\DashboardController@StudentAttendanceList');
      Route::get('/staffAttendance', 'Api\DashboardController@StaffAttendanceList');

      /*collection api*/
       Route::get('/collectionList', 'Api\DashboardController@CollectionList');
       Route::get('/cancelReceiptList', 'Api\DashboardController@CancelReceiptList');
       Route::get('/DiscountStatusList', 'Api\DashboardController@DiscountStatusList');
       /*account Apis*/
       Route::get('/IncomExpenseList', 'Api\DashboardController@IncomeExpenseList');
       /*timetable Api*/
       // Route::get('/dailytimetableschedule', 'Api\DashboardController@DailyTableList');
       Route::get('/weeklytimetableschedule', 'Api\DashboardController@WeeklyTableList');
       /*timetable Api*/
       // Route::get('/dailytimetableschedule', 'Api\DashboardController@DailyTableList');
       Route::get('/Adminweeklytimetableschedule', 'Api\DashboardController@AdminWeeklyTableList');
       Route::get('/AdminDailytimetableschedule', 'Api\DashboardController@AdminDailyTableList');

       Route::get('/user_type_list', 'Api\DashboardController@UserTypeList');
       
    //   31-08-2021
    
        Route::get('/libraryMembers', 'Api\DashboardController@libraryMembers');



    //   31-08-2021 End
    
    // 01-09-2021
    
    Route::get('/AttendanceList', 'Api\DashboardController@AttendanceListBranchWise');
    // 01-09-2021 End
    
    //04-09-2021
    
    Route::get('/headwiseTotalReport', 'Api\DashboardController@headwiseTotalReport');
    Route::get('/StudentheadwiseTotalReport', 'Api\DashboardController@StudentheadwiseTotalReport');
    //04-09-2021 end
    
    
    /*06-09-2021*/
       Route::get('/dueReport', 'Api\DashboardController@DueReport');
       Route::get('/feeReportPaymentType', 'Api\DashboardController@FeeReportPaymentType');
      /*06-09-2021*/
      
    // 07-09-2021
        Route::get('/monthList', 'Api\DashboardController@MonthList');
        Route::get('/StudentList', 'Api\DashboardController@StudentList');
        
        
        //eve
        
         /*master api list*/

       Route::get('/PurposeList', 'Api\DashboardController@PurposeList');
       Route::get('/CompliantTypeList', 'Api\DashboardController@ComplaintTypeList');
       Route::get('/SourceList', 'Api\DashboardController@SourceList');

       /*master api list*/


       /* Frontdesk routes*/
       Route::post('/enquiry', 'Api\FrontDesk\FrontdeskController@StoreEnquiry');
       Route::post('/enquiryUpdate', 'Api\FrontDesk\FrontdeskController@UpdateEnquiry');
       Route::post('/enquiryFollowup', 'Api\FrontDesk\FrontdeskController@EnquiryFollowup');
       Route::post('/deleteFollowup', 'Api\FrontDesk\FrontdeskController@deleteFollowup');
       Route::post('/visitorAdd', 'Api\FrontDesk\FrontdeskController@VisitorAdd');
       Route::post('/visitorEdit', 'Api\FrontDesk\FrontdeskController@VisitorEdit');
       Route::post('/visitorDelete', 'Api\FrontDesk\FrontdeskController@VisitorDelete');
       Route::post('/callLogAdd', 'Api\FrontDesk\FrontdeskController@callLogAdd');
       Route::post('/callLogEdit', 'Api\FrontDesk\FrontdeskController@callLogEdit');
       Route::post('/callLogDelete', 'Api\FrontDesk\FrontdeskController@callLogDelete');
       Route::post('/compliantAdd', 'Api\FrontDesk\FrontdeskController@compliantAdd');
       Route::post('/compliantEdit', 'Api\FrontDesk\FrontdeskController@compliantEdit');
       Route::post('/compliantDelete', 'Api\FrontDesk\FrontdeskController@compliantDelete');
       Route::post('/compliantStatusUpdate', 'Api\FrontDesk\FrontdeskController@compliantStatusUpdate');
        /*15-09-2021*/
     

        Route::post('/postaldispatch', 'Api\FrontDesk\FrontdeskController@postalDispatch');
        Route::post('/postalreceive', 'Api\FrontDesk\FrontdeskController@postalReceive');
        Route::post('/editpostal', 'Api\FrontDesk\FrontdeskController@postalEdit');
        Route::post('/deletepostal', 'Api\FrontDesk\FrontdeskController@postalDelete');
        Route::post('/admission', 'Api\FrontDesk\FrontdeskController@Admision');
        Route::post('/admissionedit', 'Api\FrontDesk\FrontdeskController@AdmisionEdit');
       
     /*15-09-2021*/

      /* Frontdesk routes*/

    
    
    // 07-09-2021 End
    
    // 09-09-2021
        Route::get('/enquiryFollowupList', 'Api\ServicesController@EnquiryFollowupList');
    // 09-09-2021 End
    
    
    /*13-09-2021*/
    
       Route::get('/ReferenceList', 'Api\DashboardController@Reference');
     
       Route::get('/ishandicap', 'Api\DashboardController@Is_handicap');
       Route::get('/HandicapList', 'Api\DashboardController@Handicap');
       
       Route::get('/GenderList', 'Api\DashboardController@Gender');
       Route::get('/CallType', 'Api\DashboardController@CallType');
       Route::get('/PaymentModeList', 'Api\DashboardController@PaymentModeList');
       
    /*13-09-2021 end*/
    
    /*20-09-2021*/
    Route::post('/AddCalResponse', 'Api\FrontDesk\FrontdeskController@AddCalResponse');
    Route::get('/callFollowUpList', 'Api\FrontDesk\FrontdeskController@callFollowUpList');
    Route::post('/CalResponseEdit', 'Api\FrontDesk\FrontdeskController@CalResponseEdit');
    Route::post('/CalResponseDelete', 'Api\FrontDesk\FrontdeskController@CalResponseDelete');
    Route::post('/CalResponseStatusUpdate', 'Api\FrontDesk\FrontdeskController@CalResponseStatusUpdate');
  /*20-09-2021*/
  
  
    /*27-09-2021*/
     Route::get('/staffattendancelist', 'Api\DashboardController@get_staff_attendance');
     Route::post('/update_staff_attendance', 'Api\DashboardController@update_staff_attendance');
    /*27-09-2021*/
    
    /* 30-09-2021 */
    Route::get('/DesignationList', 'Api\DashboardController@DesignationList');
    /* 30-09-2021 */
    
    
    /* 04-10-2021 */
        Route::get('/TeacherList', 'Api\DashboardController@TeacherList');
    /* 04-10-2021 */
    
    
    
    
     /* 05-10-2021 */
     /*Time table apis*/
    Route::post('/addsubject', 'Api\Timetable\TimetableController@AddSubject');
    Route::get('/subjectlist', 'Api\Timetable\TimetableController@SubjectList');
    Route::post('/subjectdelete', 'Api\Timetable\TimetableController@SubjectDelete');
    Route::post('/subjectedit', 'Api\Timetable\TimetableController@SubjectEdit');
    Route::post('/AssignSubjectTeacher', 'Api\Timetable\TimetableController@AssignSubjectTeacher');
    Route::get('/AssignSubjectTeacherList', 'Api\Timetable\TimetableController@AssignSubjectTeacherList');
    Route::post('/AssignSubjectTeacherEdit', 'Api\Timetable\TimetableController@AssignSubjectTeacherEdit');
    Route::post('/AssignSubjectTeacherDelete', 'Api\Timetable\TimetableController@AssignSubjectTeacherDelete');

    Route::get('/checkTeacherAvailbale', 'Api\Timetable\TimetableController@checkTeacherAvailbale');
    Route::post('/AddNewSchedule', 'Api\Timetable\TimetableController@AddNewSchedule');
    Route::get('/DayList', 'Api\DashboardController@DayList');
    /*Time table apis*/
   /* 05-10-2021 */
   
   
   
    /* by Gaurav Sir  */
        /* 27-10-2021 */
        // Route api
        Route::get('/routes_list', 'Api\ServicesController@routes_list');
        Route::post('/update_routes_status', 'Api\ServicesController@update_routes_status');
        Route::post('/routes_delete', 'Api\ServicesController@routes_delete');
        Route::get('/stoppage_list', 'Api\ServicesController@stoppage_list');
        Route::post('/update_stoppage_status', 'Api\ServicesController@update_stoppage_status');
        Route::post('/stoppage_delete', 'Api\ServicesController@stoppage_delete');
        /* 27-10-2021 */
        
        
        /* 28-10-2021 */
        Route::get('/vehicle_list', 'Api\ServicesController@vehicle_list');
        Route::post('/stoppage_add','Api\ServicesController@stoppage_add');
        Route::post('/stoppage_update','Api\ServicesController@stoppage_update');
        /* 28-10-2021 */
        Route::get('/get_subject_type','Api\ServicesController@get_subject_type');
        Route::get('/get_subject_by_type','Api\ServicesController@get_subject_by_type');
        Route::get('/get_group_by_subject','Api\ServicesController@get_group_by_subject');
    
    
        Route::post('/store_group_wise_class','Api\ServicesController@store_group_wise_class');

        Route::get('/get_fee_detail','Api\ServicesController@get_fee_detail');
    
		
/* New API for IOS */

Route::group(['middleware' => 'api'], function(){

	Route::post('/student_list','Api\ServicesController@student_list');
	Route::post('/student_fee_headwise','Api\ServicesController@student_fee_headwise');
	Route::post('/student_count_coursewise','Api\ServicesController@student_count_coursewise');
}); 







