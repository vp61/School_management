<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class assignment_comment extends Model
{
    protected $fillable = ['created_at','member_type','member_id','comment'];
}
