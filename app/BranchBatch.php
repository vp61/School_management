<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class BranchBatch extends Model{
    public $incrementing="true";
    protected $primaryKey="id";

    protected $table="sessionwise_branch_batch";
    protected $fillable=['session_id','branch_id','is_course_batch','created_at','created_by'];
}
?>