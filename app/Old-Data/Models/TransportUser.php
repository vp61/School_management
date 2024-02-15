<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportUser extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'routes_id', 'vehicles_id', 'user_type', 'member_id', 'status','duration','from_date','to_date','rent','total_rent','branch','session'];

    public function travellerHistory()
    {
        return $this->hasMany(TransportHistory::class, 'travellers_id','id');
    }
}
