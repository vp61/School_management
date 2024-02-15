<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at','faculty_id','from_month','to_month','branch_id','session_id','record_status' ];
}
