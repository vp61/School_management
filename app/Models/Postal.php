<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postal extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at','record_status','branch_id','session_id','to_title','date','reference_no','address','note','from_title','type','mobile'];
    
}
