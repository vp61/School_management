<h4 class="header large lighter blue"><i class="fa fa-list"></i> Assigned Subjects List</h4>
 <div class="row"> 
 	<div class="col-md-12">
 		<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<th>S.No.</th>
					<th>Teacher</th>
					<th>Subject</th>
					<th>Edit / Delete</th>
				</thead>
				@php($i=1)
				@if(isset($data['assigned']) && count($data['assigned'])>0)
					@foreach($data['assigned'] as $values)
						<tr>
							<td>{{$i}}</td>
							<td>{{$values->teacher}}</td>
							<td><?php 
                               $sub= DB::table('timetable_assign_subject as tas')->select('tas.id',DB::RAW("CONCAT(sm.title,' ( ',f.faculty,'-',sem.semester,' ) ') as title"))
                                ->leftjoin('timetable_subjects as ts','tas.timetable_subject_id','=','ts.id')
                                ->leftjoin('subject_master as sm','ts.subject_master_id','=','sm.id')
                                 ->leftjoin('faculties as f','f.id','=','ts.course_id')
                                   ->leftjoin('semesters as sem','sem.id','=','ts.section_id')
                               ->WHERE('tas.staff_id',$values->staff_id)
                               ->get();
                               
                               $s = "";
                               foreach($sub as $k=>$v){
                               	  $s .= $v->title.', ';
                               
                               }
                               echo $s = rtrim($s,", ");
							?></td>
							<td><a href="/timetable/assign/edit/{{$values->staff_id}}"><i class="fa fa-pencil green" title="Edit"></i></a>{{-- <a href="/timetable/assign/delete/{{$values->staff_id}}"><i class="fa fa-trash-o red" title="Delete"></i></a> --}}</td>
						</tr>
						@php($i++)
					@endforeach
				@endif
		</table>
 	</div>
 </div>