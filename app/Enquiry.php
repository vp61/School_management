<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
      'branch_id','org_id', 'first_name','email','enq_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference', 'refby','category_id', 'session_id','created_by','religion_id','handicap_id','no_of_child','next_follow_up','enq_status','father_name','father_occupation','mother_name','mother_occupation','whatsapp_no','previous_school'];
}
