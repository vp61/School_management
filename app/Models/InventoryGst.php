<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryGst extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'title', 'value','record_status'];
   protected $table ='inventory_gst';
}
