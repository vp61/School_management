<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class subjectMaster extends BaseModel
{
    protected $table = 'subject_master';
    protected $fillable = [ 'title', 'is_practical','created_at','updated_at','created_by','last_updated_by','record_status','is_main_subject'];
}
