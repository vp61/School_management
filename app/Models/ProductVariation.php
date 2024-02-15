<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends BaseModel
{
    protected $fillable = ['created_by','created_at','updated_by','updated_at', 'product_id', 'label_id', 'value','record_status'];
    protected $table='product_variations';
}
