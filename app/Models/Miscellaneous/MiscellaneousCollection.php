<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;
class MiscellaneousCollection extends Model
{
    protected $table="miscellaneous_collect_fee";
    protected $fillable=['id', 'reciept_no', 'student_id', 'assign_fee_id', 'amount_paid', 'discount', 'remarks', 'created_at', 'updated_at', 'payment_type', 'reference', 'status', 'created_by','reciept_date','fine'];
    // , 'session', 'course_id', 'fee_head_id'
    public $incrementing=true;
    protected $primaryKey="id";
}
