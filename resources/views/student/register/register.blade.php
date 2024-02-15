@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    @endsection

@section('content')

    <div class="main-content"  ng-app="myApp">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        @include($view_path.'.registration.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Registration
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    @include($view_path.'.includes.buttons')
                    <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                        <div class="align-right">
                        <a class="{!! request()->is('student/import*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('student.import-student') }}"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Bulk Student Registration</a>
                        </div>
                        {!! Form::open(['route' => $base_route.'.register', 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        @if(isset($admId))
                            {!!form::hidden('admission_id',$admId)!!}
                        @endif
                        @include($view_path.'.registration.includes.form')
                        <input type="hidden" name="org_id" value="1" />
                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn" type="reset">
                                    <i class="icon-undo bigger-110"></i>
                                    Reset
                                </button>

                                <button class="btn btn-info" type="submit">
                                    <i class="icon-ok bigger-110"></i>
                                    Register
                                </button>
                            </div>
                        </div>

                        <div class="hr hr-24"></div>

                        {!! Form::close() !!}

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


@endsection

@section('js')
<script>
    function handicap_cat(id) {
            var type=id.value;
            if(type==1){
            
                $('#handicap').attr('required',true);
            }else{
                $('#handicap').prop('selectedIndex','');
                $('#handicap').attr('required',false);
            }
    }
    $('#batch').on('change',function(){
       var batch_id = $(this).val();
       var course_id = $('#batch_wise_cousre').val();
       var session = $('.reg_cur_sessn').val();
       $.post(
        "{{route('getFeeByBatch')}}",
        {
            _token : "{{csrf_token()}}",
            course : course_id,
            session : session ,
            batch : batch_id
        },function(response){
            var data = $.parseJSON(response);
            $('#getFeeByBatch').html('');
            if(data.error){
                toastr.warning(data.msg,"Warning");
            }else{
                toastr.success(data.msg,"Success");
                
                $.each(data.data,function($k,$v){
                      
                   $('#getFeeByBatch').append("<tr><td></td><td>"+$v.session_name+"<input type='hidden' name='fee_masters_id[]' value='"+$v.id+"' readonly > </td><td>"+$v.fee_head_title+"</td><td >"+$v.fee_amount+"</td>   <td><input type='number' name='fee_amount[]' value='0' max='"+$v.fee_amount+"' required></td></tr>") 
                })
            }
        })
    })
    $('.getSubject').on('change',function(){
        $('#subject').html('');
        $('.selectpicker').selectpicker('refresh');
        var section = $('#sem').val();
        var course = $('#course').val();
        $.post(
            "{{route('getSubject')}}",
            {
                _token : "{{csrf_token()}}",
                section : section,
                course_id : course,
                session_id : "{{Session::get('activeSession')}}",
                branch_id : "{{Session::get('activeBranch')}}"
            },function(response){
                var data = $.parseJSON(response);
                if(data.error){
                    toastr.warning(data.msg,"Warning");
                }else{
                    toastr.success(data.msg,"Success");
                    $.each(data.data,function($k,$v){
                        $('#subject').append('<option value="'+$v.id+'">'+$v.title+'</option>');
                    });
                    $('.selectpicker').selectpicker('refresh');
                }

        });
    });
</script>
    <!-- page specific plugin scripts -->
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-comman-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
@endsection

