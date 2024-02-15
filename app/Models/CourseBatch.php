<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBatch extends Model
{
    protected $fillable=['created_at','created_by','updated_at','updated_by','start_date','end_date','capacity','course_id','course_type','session_id','status','branch_id','title'];
}
