<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class category_model extends Model{

	protected $table="category";
	protected $primary_key="id";
	protected $fillable=['category_name'];
}
?>