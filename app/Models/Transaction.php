<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaction extends BaseModel{

    protected $fillable = ['created_by', 'last_updated_by', 'date', 'tr_head_id', 'dr_amount','cr_amount', 'description','status', 'session_id', 'branch_id','note','amount','type','pay_mode','reference'];

    public function trHead()
    {
        return $this->belongsTo(TransactionHead::class, 'id');
    }

    public function session_name(){
    	return $this->belongsTo(Session_Model::class, 'id');
    }


    public function branch_name(){
    	return $this->belongsTo(Branch::class, 'id');
    }
}
