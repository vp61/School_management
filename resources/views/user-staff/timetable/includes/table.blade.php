<h4 class="header large lighter blue" style="text-align: center"> Today's Schedule: {{date('d-M-Y')}}</h4>
<hr>
<h4 class="header large lighter blue" style="">Actual Schedule</h4>
	<table class="table table-striped table-hover">
		<tr>
			@foreach($data['timetable'] as $key =>$value)
			<td style="text-align: center;box-shadow: 6px 5px 2px lightgrey;">
					<i class="fa fa-clock-o"></i> {{$value->time_from}}-{{$value->time_to}}<br><b><i class="fa fa-book"></i> 	{{$value->subject}}</b> / <?php if($value->type=='Practical'){ ?><i class="fa fa-flask"></i> <?php }else{?><i class="fa fa-file-text"></i> <?php } ?>{{$value->type}}<br><i class="fa fa-object-group"></i> {{$value->course}} ( {{$value->section}} )<br><i class="fa fa-building"></i>Room no.   {{$value->room_no}}
				</td>
			@endforeach
		</tr>
	</table>
	<h4 class="header large lighter blue" style="">Alternate Assigned Schedule</h4>
	<table class="table table-striped table-hover">
			<tr>
			@foreach($data['alt'] as $key=>$value)
				<td style="text-align: center;box-shadow: 6px 5px 2px lightgrey;">
					<i class="fa fa-clock-o"></i> {{$value->time_from}}-{{$value->time_to}}<br><b><i class="fa fa-book"></i> 	{{$value->subject}}</b> / <?php if($value->type=='Practical'){ ?><i class="fa fa-flask"></i> <?php }else{?><i class="fa fa-file-text"></i> <?php } ?>{{$value->type}}<br><i class="fa fa-object-group"></i> {{$value->course}} ( {{$value->section}} )<br><i class="fa fa-building"></i>Room no.   {{$value->room_no}}
				</td>
			@endforeach
		</tr>
	</table>
