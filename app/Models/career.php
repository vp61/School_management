<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class career extends Model
{
    protected $fillable = ['type','candidate_name', 'father_name', 'email', 'institution', 'dob','mobile','per_add','mother_teacher','experience','prt','tgt','pgt','ntt','graduation','graduation_subject','graduation_percentage','post_graduation','post_graduation_pursuing_year','is_pg_completed','post_graduation_subject','post_graduation_percentage','b_ed_pursuing_year','is_b_ed_completed','m_ed_pursuing_year','is_m_ed_completed','12_stream','12_percentage','10_percentage','board','year_of_experience','pesent_organization','classes_presently_teaching','languages_known','gender',
        'qualification', 'post_applied_for', 'current_salary', 'expected_salary', 'leaving_reason','join_day','status','record_status','followup_date'];

}
