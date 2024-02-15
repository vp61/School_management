<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamTerm extends Model
{
    protected $fillable = ['created_by', 'created_at','updated_at','updated_by','title','description','record_status','branch_id','session_id'];

}
