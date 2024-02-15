<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class E_content extends BaseModel
{
	protected $table = 'e_content';
    protected $fillable = ['created_by', 'last_updated_by','semesters_id','subjects_id','publish_date','end_date', 'chapter_no_id', 'status', 'branch_id', 'session_id', 'faculty','detail','attach_file','file','assin_book_type_id'];

   
}
