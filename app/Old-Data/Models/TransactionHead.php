<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHead extends BaseModel{

    protected $fillable = ['created_by', 'last_updated_by', 'tr_head', 'type', 'status', 'session_id', 'branch_id'];

    public function tR()
    {
        return $this->hasMany(Transaction::class, 'tr_head_id');
    }
}
