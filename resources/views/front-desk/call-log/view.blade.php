@extends('layouts.master')

@section('content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			@include('layouts.includes.template_setting')
			<div class="page-header">
				<h1>
					Call Log
					<small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Details 
                    </small>
				</h1>
			</div>
			@include('includes.flash_messages')
			<div class="row">
				<div class="col-sm-12">
					<h4 class="header large lighter blue"><i class="fa fa-list"> </i> Call Log</h4>
				</div>
				<div class="col-sm-12">
					<table class="table table-bordered table-striped">
						<tr>
							<td class="label-info white">
								Name
							</td>
							<td>
								{{$data['row']->name}}
							</td>
							<td class="label-info white">
								Mobile
							</td>
							<td>
								{{$data['row']->contact}}
							</td>
							<td class="label-info white">
								Date
							</td>
							<td>
								{{$data['row']->date}}
							</td>
						</tr>
						<tr>
							<td class="label-info white">
								Call Type
							</td>
							<td>
								@if($data['row']->call_type==1)
								<i class="fa fa-arrow-right blue"> Incoming </i>
								@else
								 <i class="fa fa-arrow-left green"> Outgoing</i> @endif
							</td>
							<td class="label-info white">
								Duration
							</td>
							<td>
								{{$data['row']->call_duration}}
							</td>
							<td class="label-info white">
								Note
							</td>
							<td>
								{{$data['row']->note}}
							</td>
						</tr>
						<tr>
							<td class="label-info white">
								Description
							</td>
							<td colspan="5">
								{{$data['row']->description}}
							</td>
						</tr>
					</table>
				</div>
				<div class="col-sm-12">
					<h4 class="header large lighter blue">
						@if(isset($row))
						<i class="fa fa-pencil"></i> Edit Response
						@else
						<i class="fa fa-plus"></i> Add Response
						@endif
					</h4>
					@include('includes.validation_error_messages')
					@if(isset($row))
						{!!Form::model($row,['route'=>[$base_route.'.updateFollowUp',$row->id],'method'=>'POST','class'=>'form-horizontal','id'=>'validation-form',"enctype"=>"multipart/form-data"])!!}
						@php($date=null)
					@else
						{!!Form::open(['route'=>[$base_route.'.addFollowUp',$data['row']->id],'method'=>'POST','class'=>'form-horizontal','id'=>'validation-form',"enctype"=>"multipart/form-data"])!!}
						@php($date=\Carbon\Carbon::now()->format('Y-m-d'))
					@endif
					<div class="form-group">
						{!!Form::label('date','Date',['class'=>'control-label col-sm-2'])!!}
						
						<div class="col-sm-2">
							{!!Form::date('date',$date,['class'=>'form-control date-picker','required'=>'required'])!!}
						</div>
						
						{!!Form::label('duration','Call Duration',['class'=>'control-label col-sm-2'])!!}
						<div class="col-sm-2">
							{!!Form::text('call_duration',null,['class'=>'form-control','placeholder'=>'Enter Call Duration'])!!}
						</div>
						
						{!!Form::label('followup','Next Follow Up Date',['class'=>'control-label col-sm-2'])!!}
						<div class="col-sm-2">
							{!!Form::date('next_follow_up',null,['class'=>'form-control date-picker'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('note','Note',['class'=>'control-label col-sm-2'])!!}
						<div class="col-sm-2">
							{!!Form::text('note',null,['class'=>'form-control','placeholder'=>'Enter Note'])!!}
						</div>
						{!!Form::label('res','Response',['class'=>'control-label col-sm-2'])!!}
						<div class="col-sm-2">
							{!!Form::text('response',null,['class'=>'form-control','placeholder'=>'Enter Response'])!!}
						</div>
						@if(isset($row))
							<div class="clearfix">
								<div class="align-right">            &nbsp; &nbsp; &nbsp;
									<button class="btn btn-info" type="submit">
										Update
									</button>
								</div>
							</div>
						@else
								<div class="clearfix">
									<div class="align-right">            &nbsp; &nbsp; &nbsp;
										<button class="btn btn-info" type="submit">
											Add
										</button>
									</div>
								</div>
						@endif	
					</div>
					{!!Form::close()!!}
				</div>
				<div class="col-sm-12">
					@php($panel="Follow Up History")
					@include('includes.data_table_header')

					<table id="dynamic-table" class="table table-striped table-hover">
						<thead>
							<tr>
								<th>S.No</th>
								<th>Date</th>
								<th>Duration</th>
								<th>Next Follow Up</th>
								<th>Note</th>
								<th>Response</th>
								<th>Status</th>
								<th>Edit|Delete|Change Status</th>
							</tr>
						</thead>
						<tbody>
							@php($i=1)
							@foreach($data['followup'] as $val)
								<tr>
									<td>{{$i}}</td>
									<td>{{\Carbon\Carbon::parse($val->date)->format('d-M-Y')}}</td>
									<td>{{$val->call_duration}}</td>
									<td>{{\Carbon\Carbon::parse($val->next_follow_up)->format('d-M-Y')}}</td>
									<td>{{$val->note}}</td>
									<td>{{$val->response}}</td>
									<td>
										
											<b class="btn btn-primary btn-minier dropdown-toggle {{ $val->follow_up_status==1?"btn-info":"btn-warning" }}" >{{ $val->follow_up_status==1?"Active":"In Active" }}</b>
										
									</td>
									<td>
										<a href="{{route($base_route.'.followUpHistory.edit',$val->id)}}" class="btn btn-primary btn-minier " title="Edit">
											<i class="fa fa-pencil"></i>
										</a>
										@ability('super-admin','super-admin')
										<a href="{{route($base_route.'.followUpHistory.delete',$val->id)}}" class="btn btn-danger btn-minier bootbox-confirm " title="Delete">
											<i class="fa fa-trash"></i>
										</a>
										@endability
										@if($val->follow_up_status==1)
											<a href="{{route($base_route.'.followUpHistory.changeStatus',[$log_id,$val->id,2])}}" class="btn btn-warning btn-minier statusChange" title="Delete" id="delete">
												In-Active
											</a>
										@else
											<a href="{{route($base_route.'.followUpHistory.changeStatus',[$log_id,$val->id,1])}}" class="btn btn-success btn-minier statusChange " title="Delete" id="delete">
												Active
											</a>


										@endif
									</td>
								</tr>
								@php($i++)
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('js')
<script src="{{ asset('assets/js/bootbox.js') }}"></script> 
<script type=""> 
          $(".statusChange").on('click', function() { 
                var $this = $(this);
                bootbox.confirm({
                        title: "<div class='widget-header'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> Delete Confirmation</h4></div>",
                        message: "<div class='ui-dialog-content ui-widget-content' style='width: auto; min-height: 30px; max-height: none; height: auto;'><div class='alert alert-info bigger-110'>" +
                        "The status of this record will be changed</div>" +
                        "<p class='bigger-110 bolder center grey'><i class='ace-icon fa fa-hand-o-right blue bigger-120'></i>Are you sure?</p>",
                        size: 'small',
                        buttons: {
                            confirm: {
                                label : "<i class='ace-icon fa fa-history'></i> Change Status",
                                className: "btn-danger btn-sm",
                            },
                            cancel: {
                                label: "<i class='ace-icon fa fa-remove'></i> Cancel",
                                className: "btn-primary btn-sm",
                            }
                        },
                        callback: function(result) {
                            if(result) {
                                location.href = $this.attr('href');
                            }
                        }
                    }
                );
                return false;
            });
</script>

@include('includes.scripts.delete_confirm')
@include('includes.scripts.dataTable_scripts')
@endsection