<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ResultType extends Model
{
    protected $fillable = ['created_by', 'created_at','updated_at','updated_by','record_status','branch_id','session_id','course_id','section_id','result_type_id'];
    protected $table = 'exam_result_type';

}
