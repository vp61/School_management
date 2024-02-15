<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
      'branch_id','org_id', 'first_name','email','enq_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference', 'refby','category_id', 'session_id','created_by'];
}
