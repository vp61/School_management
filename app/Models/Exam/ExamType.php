<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    protected $fillable = ['created_by', 'created_at','updated_at','updated_by','title','description','record_status','term_id','branch_id','session_id'];
    protected $table = 'exam_type';

}
