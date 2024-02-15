<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleMaintenance extends BaseModel
{
    protected $fillable = ['created_by', 'updated_by', 'updated_at', 'created_at', 'record_status', 'maintenance_charge', 'performed_by','work_performed','maintenance_date','problem','vehicle_id','branch_id','session_id'];

    protected $table= 'vehicle_maintenance';
    
}
