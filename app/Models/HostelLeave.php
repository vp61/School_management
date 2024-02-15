<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelLeave extends BaseModel
{
    protected $fillable = ['creatd_at','updated_at','created_by', 'updated_by', 'resident_id', 'leave_from', 'leave_to', 'reason', 'return_date', 'remark', 'branch_id','session_id','record_status'];

   protected $table= 'hostel_leave';
}
