<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FineSettings extends Model
{
    protected $fillable = ['created_at', 'created_by','updated_at','updated_by','due_month_id','faculty_id','fee_head_id','start_date','daily_fine','monthly_fine','on_minimum_due','record_status','branch_id','session_id'];
    protected $table = 'fine_settings';
}
