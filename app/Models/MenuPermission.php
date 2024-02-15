<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPermission extends Model
{
    protected $fillable = ['created_by', 'group', 'name', 'display_name', 'parent_id','route','description','updated_at','created_by','updated_by'];
    protected $table = 'permissions';
}
