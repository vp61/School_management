<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends BaseModel
{
    protected $table = 'chapter_no';
    protected $fillable = [ 'title','timetable_subjects_id','faculty','semesters_id','created_at','created_by','updated_at','updated_by','status','session_id','branch_id'];
}
