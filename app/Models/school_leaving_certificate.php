<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class school_leaving_certificate extends BaseModel
{
   protected $table ='school_leaving_certificate';
    protected $fillable = ['branch_id','created_at','status','updated_at','student_id','book_no','si_no','admi_no','student_name', 'father_name','mother_name','nationality','schedule_caste','first_admission_date','date_of_birth','pupil_last_studied','last_percentage_scored','annual_examination_last','examination_last_result','whether_failed','subjects_name','whether_qualified','school_dues_paid','fee_concession','academic_session','total_no_presence','whether_ncc_cadet','games_played','general_conducts','date_of_application','struck_off','date_issue_certificate','leaving_school','other_remark','session_id','detail'];
}
