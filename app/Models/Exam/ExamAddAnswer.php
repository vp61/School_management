<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamAddAnswer extends Model
{
    protected $fillable = ['created_by', 'created_at','updated_at','updated_by','exam_id','exam_question_id','student_id','obtained_mark','option_1_answer','option_2_answer','option_3_answer','option_4_answer','option_5_answer','option_6_answer','option_1_image','option_2_image','option_3_image','option_4_image','option_5_image','option_6_image','record_status'];
    protected $table = 'exam_question_answer';
}
