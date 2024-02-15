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
                            Add
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                        @include('includes.flash_messages')
                        <div class="row">
                            <div class="col-xs-4">  
                                <h4 class="header large lighter blue">   <i class="fa fa-plus bigger-110"></i> Add</h4> 
                                 {!!Form::open(['route'=>$base_route.'.store','method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 
                                        @include($view_path.'.includes.add') 
                                 <div class="clearfix form-actions">
                                    <div class="align-right">            &nbsp; &nbsp; &nbsp;
                                            <button class="btn btn-info" type="submit" id="filter-btn">
                                                 <i class="fa fa-plus bigger-110"></i>
                                                    Add 
                                            </button>
                                    </div>
                            </div>        
                                  {!!Form::close()!!}
                            </div>
                            <div class="col-xs-8">
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
 <script>
     function loadCourse($this){
        var branch="{{Session::get('activeBranch')}}"
       $.ajax({
            type: 'POST',
            url: "{{route('loadCourseByType')}}",
            data:{
                _token: "{{csrf_token()}}",
                courseType: $this.value,
                branch_id: branch
            },
            success: function(response){
                var data = $.parseJSON(response);
               
                if(data.error){
                   
                    toastr.warning(data.message, "warning");
                }else{
                   
                    $('#course').html('').append('<option value="">--Select {{ env('course_label') }}--</option>');
                    $.each(data.data,function($key,$val){
                        $('#course').append('<option value="'+$val.id+'">'+$val.faculty+'</option>');
                    });
                }
            }
       });
     }
 </script>

@endsection