@extends('layouts.master')
@section('css')
    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />

@endsection

@section('content')
	<div class="main-content">
		<div class="main-content-inner">
			<div class="page-content">
				 @include('layouts.includes.template_setting')
				 <div class="page-header">
				 	<h1>
				 		@include($view_path.'.generate.includes.breadcrumb-primary')
				 		<small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                           Generate
                        </small>
				 	</h1>
				 
				 </div>
                   @include('includes.flash_messages')
                    @include($view_path.'.includes.buttons')
				 <div class="row">
				 	<div class="col-md-12">

				 		{!!Form::open(['route'=>$base_route.'.generate','method'=>'POST','class'=>'form-horizontal','id'=>'validation-form',"enctype"=>"multipart/form-data"])!!}
				 			@include($view_path.'.generate.includes.form')
				 		{!!Form::close()!!}	
				 	</div>
				 	
				 </div>
				
			</div><!-- /.page-content -->
			
		</div>
		
	</div><!-- /.main-content -->
	  
@endsection

@section('js')
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script type="text/javascript">
	function loadStudent(){
            var course=document.getElementById('course').value;
            var section=document.getElementById('section').value;
           $.ajax({
            type:'POST',
            url: '{{route('hostel.resident.load-student')}}',
            data: {
                _token : '{{csrf_token()}}',
                course : course,
                section : section
            },
            success:function(response){
                var data = $.parseJSON(response);
                if(data.error){
                    $.notify(data.message,"warning");
                }else{
                	$('#student').select2();
                    $('#student').html(' ').append('<option value="">--Select Student--</option>');
                    $.each(data.student,function(key,val){
                        $('#student').append('<option value="'+val.id+'">'+val.name+'</option>');
                    });
                }
            }
           });
      }     
</script>
@endsection