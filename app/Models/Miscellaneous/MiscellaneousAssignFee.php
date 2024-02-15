<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;

class MiscellaneousAssignFee extends Model{

	public $incrementing=true;
	protected $table="miscellaneous_assign_fee";
	protected $primaryKey="id";
	protected $fillable = ['id', 'branch_id', 'session_id', 'course_id', 'subject_id', 'student_id', 'fee_head_id', 'fee_amount', 'created_at', 'status', 'updated_at', 'created_by','deleted_at','deleted_by','updated_by'];
   
}
