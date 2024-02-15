<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'reg_no', 'join_date', 'designation', 'first_name',  'middle_name', 'last_name',
        'father_name', 'mother_name', 'date_of_birth', 'gender', 'blood_group', 'nationality','mother_tongue', 'address', 'state', 'country',
        'temp_address', 'temp_state', 'temp_country', 'home_phone', 'mobile_1', 'mobile_2', 'email', 'qualification',
        'experience', 'experience_info', 'other_info','staff_image', 'status', 'branch_id','observer','zoom_api_key','zoom_secret_key','zoom_email','zoom_password','zoom_jwt'];

    public function payrollMaster()
    {
        return $this->hasMany(PayrollMaster::class, 'staff_id');
    }

    public function paySalary()
    {
        return $this->hasMany(SalaryPay::class, 'staff_id');
    }

}
