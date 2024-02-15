<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;

class MiscellaneousHead extends Model
{
    protected $table = 'miscellaneous_heads';
    protected $fillable = ['created_by', 'last_updated_by', 'fee_head_title', 'slug', 'status','parent_id'];
}
