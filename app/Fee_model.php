<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fee_model extends Model{
    
    protected $table="assign_fee";
    protected $primaryKey="id";
    protected $fillable=["id", "branch_id", "session_id", "course_id", "subject_id", "student_id", "fee_head_id", "fee_amount", "created_at", "status", "updated_at"];
    public $incrementing=true;
}
