<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model{
    public $incrementing="true";
    protected $primaryKey="id";

    protected $table="branches";
    protected $fillable=['id', 'org_id', 'branch_name', 'branch_title', 'branch_address', 'branch_email', 'branch_mobile', 'branch_logo', 'branch_descriptiojn', 'branch_manager', 'Merchant_Key', 'Merchant_Salt', 'created_at', 'updated_at'];
}
?>