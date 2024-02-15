<h4 class="header large lighter blue"><i class="fa fa-list"></i> Subjects List</h4>
 <div class="row"> 
 	<div class="col-md-12">
 		<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<th>S.No.</th>
					<th>{{ env('course_label') }}</th>
					<th>Section</th>
					<th>Subject</th>
					<th>Edit /Delete </th>
				</thead>
				
				@php($i=1)

				@if(isset($data['subject']) && count($data['subject'])>0)
					@foreach($data['subject'] as $values)
                         
						<tr>
							<td>{{$i}}</td>
							<td>{{$values->course}}</td>
							<td>{{$values->section}}</td>
							<td><?php 
                               $sub= DB::table('timetable_subjects as ts')->select('ts.id','subject_master_id','sm.title')
                                ->leftjoin('subject_master as sm','ts.subject_master_id','=','sm.id')
                               ->WHERE('ts.course_id',$values->course_id)
                               ->WHERE('ts.section_id',$values->section_id)
                               ->get();
                               $s = "";
                               foreach($sub as $k=>$v){
                               	if(!empty($v->extra_fee)){
                              	 $s .= $v->title.', ';                              	
                               	}
                               	else{
                               		 $s .= $v->title.', ';
                               	}
                               }
                               echo $s = rtrim($s,", ");
							?></td> 
							<td><a href="{{url('timetable/subject/edit' . '/'. $values->course_id .'/'. $values->section_id)}}"><i class="fa fa-pencil green" title="Edit"></i></a> <!-- <a href="/timetable/subject/delete/{{$values->id}}"><i class="fa fa-trash-o red" title="Delete"></i></a> --></td>
						</tr>
						@php($i++)
					@endforeach
				@endif
		</table>
 	</div>
 </div>