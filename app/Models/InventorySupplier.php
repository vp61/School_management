<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySupplier extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'name', 'mobile','gstin','email','address','alternate_mobile','record_status'];
   protected $table ='inventory_suppliers';
}
