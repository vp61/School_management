@extends('layouts.master')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            <div class="page-header">
                <h1> Miscellaneous Fees Manager 
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Cancelled Receipts
                    </small>
                </h1>
            </div>
            {{--Search Form--}}
            <h4 class="header large lighter blue"><i class="fa fa-search"></i> Search Receipts</h4>
            <form autocomplete="off" method="GET" action="{{route('cancelled_receipts')}}">
			    <div class="clearfix">
			        <div class="form-group">
			            <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
			            <div class="col-sm-3">
			                {!! Form::select('faculty',$data['course'], null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);']) !!}
			            </div>
			            {!! Form::label('reg_date', 'Date', ['class' => 'col-sm-2 control-label']) !!}
			            <div class=" col-sm-5">
			                <div class="input-group ">
			                    {!! Form::date('start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
			                    <span class="input-group-addon">
			                        <i class="fa fa-exchange"></i>
			                    </span>
			                    {!! Form::date('end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
			                    @include('includes.form_fields_validation_message', ['name' => 'reg_start_date'])
			                    @include('includes.form_fields_validation_message', ['name' => 'reg_end_date'])
			                </div>
			            </div>
			    	</div>
				</div>
				<div class="clearfix">
				    <div class="form-group">
				        <label class="col-sm-2 control-label">Receipt Payment Type</label>
				        <div class="col-sm-3">
				            {!!Form::select('pay_type',$data['pay_type'],null,['class'=>'form-control'])!!}            
				        </div>
				        <label class="col-sm-2 control-label">Student Name</label>
				        <div class="col-sm-2">
				        	{!!Form::text('name',null,['class'=>'form-control','palceholder'=>'Enter Student Name'])!!}
				        </div>
				        <label class="col-sm-1 control-label">Reg. No</label>
				        <div class="col-sm-2">
				            {!!Form::text('reg_no',null,['class'=>'form-control','palceholder'=>'Enter Registration No.'])!!}
				        </div>
				    </div>
				</div>
				<div class="clearfix">
				    <div class="form-group">
				    	<label class="col-sm-2 control-label">Cancelled By</label>
				         <div class="col-sm-3">
				            {!!Form::select('cancelled_by',$data['user'],null,['class'=>'form-control'])!!}
				        </div>
				        <label class="col-sm-2 control-label">Receipt Number</label>
				         <div class="col-sm-2">
				            {!!Form::text('receipt_no',null,['class'=>'form-control'])!!}
				        </div>
				        
				         <div class="col-md-3 align-right">        &nbsp; &nbsp; &nbsp;

				        <button class="btn btn-info" type="submit" id="filter-btn">
				            <i class="fa fa-filter bigger-110"></i>
				            Search
				        </button>
				    </div>
				    </div>
				</div>
			</form>

			<h4 class="header large lighter blue"><i class="fa fa-list"></i> Cancelled receipt list</h4>
			<div class="table-responsive">
			<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>S.No</th>
						<th>{{env('course_label')}}</th>
						<th>Section</th>
						<th>Reg. No</th>
						<th>Name</th>
						<th>Receipt No.</th>
						<th>Paid</th>
						<th>Discount</th>
						<th>Receipt Type</th>
						<th>Cancel Date</th>
						<th>Cancelled By</th>
					</tr>
				</thead>
				<tbody>
					@if(count($data['receipts'])>0)
						@php($i=1)
						@foreach($data['receipts'] as $key=>$val)
							<tr>
								<td>{{$i}}</td>
								<td>{{$val->course}}</td>
								<td>{{$val->sem}}</td>
								<td>{{$val->reg_no}}</td>
								<td>{{$val->student_name}}</td>
								<td>{{$val->reciept_no}}</td>
								<td>{{$val->amount_paid}}</td>
								<td>{{$val->discount}}</td>
								<td>{{$val->payment_type}}</td>
								<td>{{\Carbon\Carbon::parse($val->log_created_at)->format('d-M-Y')}}</td>
								<td>{{$val->cancel_by}}</td>
							</tr>
						@endforeach
					@endif
				</tbody>
				
			</table>
			</div>
			
        </div>
    </div>
</div>        
@endsection
@section('js')
	@include('includes.scripts.dataTable_scripts')
@endsection