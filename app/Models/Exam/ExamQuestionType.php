<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamQuestionType extends Model
{
    protected $fillable = ['created_by', 'created_at','updated_at','updated_by','title','description','record_status'];

}
