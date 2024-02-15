<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $fillable = [
       'reference_no','payment_type','branch_id','org_id', 'first_name','email','admission_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference','admission_fee','category_id','session_id', 'id','created_by','updated_by','enquiry_id','religion_id','handicap_id','form_no','father_name','comm_country','comm_state','comm_city','comm_address','father_occupation','mother_name','mother_occupation','whatsapp_no','previous_school','father_annual_income','father_aadhar_no','mother_annual_income','mother_aadhar_no','age_in_year_as_on_1april'
    ];
}
