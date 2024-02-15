<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Units extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'title', 'description','record_status'];
   protected $table ='inventory_units';
}
