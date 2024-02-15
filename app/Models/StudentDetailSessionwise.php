<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentDetailSessionwise extends BaseModel{
	
	protected $table="student_detail_sessionwise";
	
	protected $fillable = ['id', 'course_id', 'student_id', 'session_id', 'Status', 'Division', 'Semester', 'created_by', 'created_at', 'updated_at', 'promoted_session', 'promoted_course'];

	public function students(){
		return $this->hasMany(Student::class, 'id', 'student_id');
	} 
}
?>