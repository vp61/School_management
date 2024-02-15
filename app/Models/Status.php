<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'title','record_status'];
   
}
