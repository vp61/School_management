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
                         Edit Subjects
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
                	<div class="col-md-4">
                     <h4 class="header large lighter blue"><i class="fa fa-pencil"></i> Edit</h4>
                     <!-- assign subject to class/course -->
                        <form class="form-horizontal disable_save_form" id="form"  role="form" method="POST" action="{{ route($base_route.'.edit', ['courseid' => $courseid,'sectionid'=>$sectionid]) }}" enctype="multipart/form-data">
                       {{ csrf_field() }}

                    <div class="form-group">
                        {!!Form::label('course',env('course_label'),['class'=>'col-sm-6 control-label'])!!}
                        <div class=" col-md-6">
                            {{ Form::select('course', $data['course'], $courseid, ['class'=>'form-control', 'required'=>'required','onchange'=>'loadSemesters(this)'])}}
                        </div>
                         
                    </div>
                    <div class="form-group">
                        {!!Form::label('section','Section',['class'=>'col-sm-6 control-label'])!!}
                        <div class="col-md-6">
                            {!!Form::select('section',$data['section'],$sectionid,['class'=>'form-control semester_select','required'=>'required'])!!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!!Form::label('subject','Subject',['class'=>'col-sm-6 control-label'])!!}
                        <div class="col-md-6">
                            <table class="table" >
                                
                                @foreach( $data['sub'] as $k=>$v)
                                   <tr>
                                        <td  style="border:none;">
                                                
                                                <?php
                                                    $chk = $v->subject_master_id?'Checked':'';
                                                ?>
                                                <input type="checkbox" class="custom-control-input" id="customCheck{{$v->id}}" name="subject[{{$v->id}}]" value="{{$v->id}}" {{$chk}}>
                                                 <label class="custom-control-label" for="customCheck{{$v->id}}">{{$v->title}}</label>
                                         </td>
                                          <!-- subjectPriority -->
                                         <td style="border:none;">
                                                  <input type="number" name="sub_priority[{{$v->id}}]" class="form-control" value="{{$v->sub_priority}}">
                        
                                         </td>
                                         <!-- subjectPriority -->
                                    </tr>
                                @endforeach
                            </table>
                            
                        </div>
                    </div>
                   <!-- assign subject to class/course -->
                    
                      
                  

                    
                    <div class="align-right">
                                <button type="submit" class="btn btn-sm btn-primary " >
                                    Update Subject
                                </button>
                                &nbsp;&nbsp;&nbsp;
                            </div>
                        {!!Form::close()!!}
                	</div>
                    
                </div>
            </div> 
        </div>
    </div>          

@endsection

@section('js')
 @include('includes.scripts.dataTable_scripts')
  <script type="text/javascript">
        $('.new_row').click(function(){
        var data=document.getElementById('head_tbl').innerHTML;
        var last_id=$('#variation_tbl tr:last').attr('id');
        $('#variation_tbl').append('<tr>'+data+'</tr>');
    });
        /*load section code*/
        function loadSemesters($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-semester') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('select.semester_select').html('').append('<option value="0">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('select.semester_select').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                         $('select.selectpicker').selectpicker('refresh');
                    }
                }
            });

        }
        /*load section code end */
    </script>
@endsection