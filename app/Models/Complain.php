<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complain extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'complain_by', 'mobile','date','description','email','assigned','action_taken','record_status','branch_id','session_id','note','complain_status','complain_type','source_id'];
    
}
