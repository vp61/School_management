<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallLog extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'name', 'contact','date','description','follow_up_date','call_duration','note','record_status','call_type','branch_id','session_id'];
    
}
