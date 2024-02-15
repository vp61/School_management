<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryPurchase extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'product_id', 'supplier_id','	reference','purchase_date','purchase_status','quantity','unit_price','record_status'];
   protected $table ='inventory_purchase';
}
