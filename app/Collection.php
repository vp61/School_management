<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Collection extends Model
{
    protected $table="collect_fee";
    protected $fillable=['id', 'reciept_no', 'student_id', 'assign_fee_id', 'amount_paid', 'discount', 'remarks', 'created_at', 'updated_at', 'payment_type', 'reference', 'status', 'created_by','reciept_date','fine'];
    // , 'session', 'course_id', 'fee_head_id'
    public $incrementing=true;
    protected $primaryKey="id";
}
