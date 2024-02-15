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
                          Add Subjects
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
                     <h4 class="header large lighter blue"><i class="fa fa-plus"></i> Add</h4>
                        {!!Form::open(['route'=>$base_route.'.store','method'=>'POST','class'=>'form-horizontal','id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
                        		  @include($view_path.'.includes.form')
                        {!!Form::close()!!}
                	</div>
                	<div class="col-md-8">
                		@include($view_path.'.includes.table')
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