<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'institute', 'salogan', 'address','phone','email','website', 'favicon', 'email', 'logo',
        'print_header', 'print_footer','receipt_footer', 'facebook', 'twitter', 'linkedIn', 'youtube', 'googlePlus',
        'instagram', 'whatsApp', 'skype', 'pinterest','wordpress', 'status','online_admission_session','online_admission_min_pay','live_class_scheduling'];
}
