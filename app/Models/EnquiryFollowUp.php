<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryFollowUp extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at','followup_date','enquiry_id','next_followup','note','record_status','response'];
    protected $table='enquiry_followup';
}
