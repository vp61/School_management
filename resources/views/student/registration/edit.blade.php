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
                        @include($view_path.'.registration.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Edit  Registration
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <div class="row">
                    <div class="col-xs-12 ">
                    @include($view_path.'.includes.buttons')
                        <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                        {!! Form::model($data['row'], ['route' => [$base_route.'.update', $data['row']->id], 'method' => 'POST', 'class' => 'form-horizontal',
                   'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        {!! Form::hidden('id', $data['row']->id) !!}
                        {{--{!! Form::hidden('guardians_id', $data['row']->guardians_id) !!}--}}
                        @include($view_path.'.registration.includes.form')
                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn btn-info" type="submit">
                                    <i class="icon-ok bigger-110"></i>
                                    Update
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
function deleteItem(id){
                    $.ajax({ 
                   type: "POST", 
                   url:"{{url('student/delete-sibling')}}", 
                   data:{id:id,_token : "{{csrf_token()}}"},
                   success: function(result) {
                    toastr.success('Record Deleted!!!',"Success");
                     $("#head_tbl" + id).remove();
                   }
               }); 
          }
    function handicap_cat(id) {
            var type=id.value;
            if(type==1){
            
                $('#handicap').attr('required',true);
            }else{
                $('#handicap').prop('selectedIndex','');
                $('#handicap').attr('required',false);
            }
    }
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
    
    $('.new_row').click(function(){
        var data=document.getElementById('head_tbl').innerHTML;
        var last_id=$('#variation_tbl tr:last').attr('id');
        data = data.replace('futureSelectPicker','selectpicker');
        $('#variation_tbl').append('<tr>'+data+'</tr>');
        $('.selectpicker').selectpicker('refresh');
    });
</script>
    <!-- page specific plugin scripts -->
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-comman-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
    {{--@include('includes.scripts.table_tr_sort')--}}
@endsection


