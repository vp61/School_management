<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUpHistory extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at','date','note','record_status','branch_id','session_id','call_duration','next_follow_up','call_log_id','follow_up_status','response'];
    protected $table='follow_up_history';
}
