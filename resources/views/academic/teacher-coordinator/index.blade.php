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
                        <div class="row">
                            <div class="col-xs-4">  
                                <h4 class="header large lighter blue"> 
                                @if(isset($data['row']))
                                     <i class="fa fa-pencil bigger-110"></i> Edit
                                @else     

                                  <i class="fa fa-plus bigger-110"></i> Add
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
 <script type="text/javascript">
   
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
      
 </script>


@endsection