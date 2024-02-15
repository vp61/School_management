<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'title','parent_id', 'description','record_status'];
   
}
