<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDailyEntry extends BaseModel
{
    protected $fillable = ['created_by', 'updated_by', 'updated_at', 'created_at', 'record_status', 'vehicle_id', 'date','distance','fuel','receipt_no','fuel_amount','branch_id','session_id'];

    protected $table= 'transport_daily_entry';
    
}
