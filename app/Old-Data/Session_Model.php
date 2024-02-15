<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session_Model extends Model{

	public $incrementing=true;
	protected $table="session";
	protected $primaryKey="id";
	protected $fillable=['id', 'session_name', 'created_at', 'updated_at', 'status'];
}
