<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson_plans extends BaseModel
{
	protected $table = 'lesson_plans';
    protected $fillable = ['created_by', 'last_updated_by', 'months_id','semesters_id','subjects_id','publish_date',
        'end_date', 'type', 'chapter_no_id', 'topic', 'unit', 'no_h/w', 'status', 'branch_id', 'session_id', 'faculty','detail','attach_file','file','sub_en_activity','serial_no'];

   
}
