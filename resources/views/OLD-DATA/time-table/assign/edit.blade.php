@extends('layouts.master')

@section('css')
@endsection

@section('content')
	<div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                       {{$panel}} Manager
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                          Assign Subjects
                        </small>
                    </h1>
                </div>
                <div class="row">
                	<div class="col-md-12">
                		@include('time-table.includes.buttons')
                	</div>
                </div>
                @include('includes.flash_messages')
                <div class="row">
                	<div class="col-md-12">
                	 <h4 class="header large lighter blue"><i class="fa fa-pencil"></i> Edit</h4>
                        {!!Form::open(['route'=>[$base_route.'.edit',$id],'method'=>'POST','class'=>'form-horizontal','id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
                            <div class="form-group">
                                {!!Form::label('subject','Subject',['class'=>'col-sm-2 control-label'])!!}
                                <div class="col-md-4">
                                    {!!Form::select('subject',$data['subject'],$data['assigned']->timetable_subject_id,['class'=>'form-control','required'=>'required'])!!}
                                </div>
                                 {!!Form::label('teacher','Teacher',['class'=>'col-sm-2 control-label'])!!}
                                <div class="col-md-4">
                                    {!!Form::select('teacher',$data['teacher'],$data['assigned']->staff_id,['class'=>'form-control','required'=>'required'])!!}
                                </div>
                         </div>
               
                        <div class="align-right">
                                    <button type="submit" class="btn btn-sm btn-primary " >
                                        Update
                                    </button>
                                    &nbsp;&nbsp;&nbsp;
                                </div>
                            {!!Form::close()!!}
                        </div>
                	</div>
                	
                </div>
            </div> 
        </div>
    </div>          

@endsection

@section('js')
 @include('includes.scripts.dataTable_scripts')
@endsection