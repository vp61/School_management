<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherCoordinator extends BaseModel
{
    protected $table = 'teacher_coordinator';
    protected $fillable = [ 'teacher_id', 'faculty_id','section_id','created_at','updated_at','created_by','last_updated_by','record_status','branch_id','session_id','type'];
}
