<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model{

	public $incrementing=true;
	protected $table="payment_type";
	protected $primaryKey="id";
	protected $fillable=['id', 'type_name', 'status'];
}
