<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'purpose', 'name', 'email', 'contact','id_proof','no_of_people','date','in_time','out_time','note','image','record_status','branch_id','session_id'];
    protected $table='visitors_book';
}
