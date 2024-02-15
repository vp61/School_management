<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class StudentPromotion extends Model{
    protected $fillable=['course_id', 'student_id', 'session_id', 'Status', 'Division', 'Semester', 'created_by', 'created_at', 'updated_at', 'promoted_session', 'promoted_course'];
    protected $table="student_detail_sessionwise";

    protected $primaryKey="id";
    public $incrementing="true";
}
