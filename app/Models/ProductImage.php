<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends BaseModel
{
    protected $fillable = ['created_by','created_at','product_id','product_id','image_name','record_status'];
    protected $table='product_image';
}
