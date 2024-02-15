<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    protected $fillable = ['created_at', 'created_by', 'topic','start_time','duration','faculty_id',
        'section_id', 'session_id', 'branch_id', 'status','meeting_id','meeting_password','join_url','start_url','email','host_status'];

}
