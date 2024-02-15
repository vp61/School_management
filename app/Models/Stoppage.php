<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stoppage extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at','title','distance','fee_amount','active_status','record_status','route_id'];
   
}
