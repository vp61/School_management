<h4 class="header large lighter blue" style="text-align: center"> Today's Schedule: {{date('d-M-Y')}}</h4>


	<table class="table table-striped table-hover">
		<tr>
			@foreach($data['timetable'] as $key =>$value)
			<td style="text-align: center;box-shadow: 6px 5px 2px lightgrey;"><i class="fa fa-clock-o"></i> {{$value->time_from}}-{{$value->time_to}}<br><i class="fa fa-book"></i> 	{{$value->subject}}<br> <i class="fa fa-user"></i>  <b <?php if($value->present!=1){?> style="color:red;"<?php } ?>>{{$value->staff}}</b><br><b style="color: green;">{{$value->altTeacher}}</b><br><?php if($value->type=='Practical'){ ?><i class="fa fa-flask"></i> <?php }else{?><i class="fa fa-file-text"></i> <?php } ?>{{$value->type}}<br><i class="fa fa-building"></i>  {{$value->room_no}}</td>
			@endforeach
		</tr>
	</table>
	
