<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'title', 'brand_id', 'unit_id', 'category_id','sku','isbn','alert_quantity','sub_category','price','gst','amount','record_status'];
    protected $table='inventory_products';
}
