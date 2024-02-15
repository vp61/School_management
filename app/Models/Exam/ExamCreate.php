<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamCreate extends Model
{
    protected $fillable = ['created_by', 'created_at','updated_at','updated_by','title','description','record_status','branch_id','session_id','term_id','type_id','faculty_id','section_id','subject_id','mode_id','paper_type','max_mark','pass_mark','date','start_time','end_time','publish_status','result_status','room_no','grading_type'];
    protected $table = 'exam_create';
}
