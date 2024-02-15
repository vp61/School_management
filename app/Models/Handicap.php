<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Handicap extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at','title','amount','record_status'];
    
}