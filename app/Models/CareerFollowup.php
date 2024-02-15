<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerFollowup extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at','record_status','career_id','followup_date','next_followup_date','response','career_status'];
     protected $table = 'career_followup';
   
}
