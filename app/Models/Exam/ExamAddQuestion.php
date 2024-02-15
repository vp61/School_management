<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamAddQuestion extends Model
{
    protected $fillable = ['created_by', 'created_at','updated_at','updated_by','exam_id','question_title','question_description','question_image','question_type','mark','option_1','option_2','option_3','option_4','option_5','option_6','option_1_image','option_2_image','option_3_image','option_4_image','option_5_image','option_6_image','correct_answer','record_status','is_required'];
    protected $table = 'exam_question';
}
