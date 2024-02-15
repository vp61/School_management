<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transport_collect_fee extends Model
{
      protected $fillable = ['created_by', 'updated_by', 'std_id', 'transport_user_id', 'status', 'amount_paid', 'pay_mode'];
}
