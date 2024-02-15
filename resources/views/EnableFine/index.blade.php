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
                       {{$panel}}
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                           @if(isset($data['row']))
                            Edit
                           @else
                            Add
                           @endif 
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                   
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        <div class="row">
                          
                            <div class="col-xs-12 col-sm-12 col-md-12">  
                                <h4 class="header large lighter blue"> 
                                @if(isset($data['row']))
                                     <i class="fa fa-pencil bigger-110"></i> Edit {{$panel}}
                                @else     

                                  <i class="fa fa-plus bigger-110"></i> Add {{$panel}}
                                 @endif 
                              </h4>
                              @if(isset($data['row']))
                                 {!!Form::model($data['row'],['route'=>[$base_route.'.edit',$data['row']->id],'method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
                              @else 
                                 {!!Form::open(['route'=>$base_route.'.store','method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 
                              @endif          
                                        @include($view_path.'.includes.form')  
                                  {!!Form::close()!!}
                            </div>
                          
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                @include($view_path.'.includes.table')
                            </div>
                        </div>
                        <div class="hr hr-18 dotted hr-double"></div>
                       
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
 @include('includes.scripts.dataTable_scripts')
 @include('includes.scripts.delete_confirm')
 <script>
     $('.getHead').on('change',function(){
        var month_id = $('#month_id').val();
        var faculty_id = $('#faculty_id').val();
        var error = false;

        $('#head_id').html('').append('<option value=""> Select </option>')

        if(!month_id){
            toastr.warning('Please Select Month','Warning');
            error = true;
        }

        if(!faculty_id){
            toastr.warning('Please Select {{env("course_label")}}','Warning');
            error = true;
        }

        if(error){
            $('.submitButton').attr('disabled',true);
            return false;
        }else{
            $('.submitButton').attr('disabled',false);
        }

        $.ajax({
            url:"{{route('enableFine.getHead')}}",
            method: "POST",
            data:{
                _token: "{{csrf_token()}}",
                month_id : month_id,
                faculty_id : faculty_id,
                session_id : "{{Session::get('activeSession')}}",
                branch_id : "{{Session::get('activeBranch')}}",
            },
            success:function(response){
                var data = $.parseJSON(response);

                if(data.error){
                    $('.submitButton').attr('disabled',true);
                    toastr.warning('No head found','Warning');
                }else{
                    toastr.success('Data Found','Success');
                    $('#head_id').html('').append('<option value=""> -- Select Head -- </option>')
                    $.each(data.data,function(k,v){
                        
                        $('#head_id').append("<option value='"+k+"'> "+v +"</option>");
                    })
                }
            },
            error:function(response){
                 toastr.error('Something went wrong','Error');
            }
        })

     })
 </script>
@endsection