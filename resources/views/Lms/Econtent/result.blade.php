@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.min.css') }}" />
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>

                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include($view_path.'.includes.buttons')
                    <div class="col-xs-12 ">
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                            <div class="form-horizontal">
                            @include($view_path.'.includes.table')
                            </div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    <!-- inline scripts related to this page -->
 
    <script type="text/javascript">
        

        function loadSemesters($this){
            $.ajax({
                type: 'POST',
                url: '{{ route('Lms.Lesson_plans.find-semester') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.semesters_id').html('').append('<option value="0">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('.semesters_id').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });
        }
        function loadSubject($this) {
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semesters_id"]').val();
            if (faculty == 0) {
                toastr.info("Please, Select Faculty", "Info:");
                return false;
            }

            if (semester == 0) {
                toastr.info("Please, Select Section", "Info:");
                return false;
            }

            if (!semester)
                toastr.warning("Please, Choose Section.", "Warning");
            else {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('Lms.Lesson_plans.find-subject') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        faculty_id: faculty,
                        semester_id: semester,
                        session_id : "{{Session::get('activeSession')}}",
                        branch_id : "{{Session::get('activeBranch')}}",
                    },
                    success: function (response) {
                        var data = $.parseJSON(response);
                        if (data.error) {
                            $('.semester_subject').html('')
                            toastr.warning(data.error, "Warning:");
                        } else {
                            $('.semester_subject').html('').append('<option value="0">Select Subject</option>');
                            $.each(data.subjects, function (key, valueObj) {
                                $('.semester_subject').append('<option value="' + valueObj.id + '">' + valueObj.title + '</option>');
                            });
                            toastr.success(data.success, "Success:");
                        }
                    }
                });
            }

        }
       
    function showtopic($this){
            // alert('result');
          // alert($this.id);
        var id=$this.id
        // alert(id);
        // var row_id= $this.id;
        // alert(row_id);

        var i=1;
        // var fac= {{$fac}};
        // var sem= {{$sem}};
        // var sub= {{$sub}};
        //alert(sem);
        var faculty=$('#course').val();
        var sub=$('.subjects_id_'+id).val();
        var fac=$('.faculty_id_'+id).val();
        var sem=$('.section_id_'+id).val();
        // alert(sub);
        // alert(fac);
        // alert(sem);
        //var faculty = $('select[name="faculty"]').val();
        //alert(faculty);
        //var semester = $('select[name="semesters_id"]').val();
        //var subject =$('select[name="subjects_id"]').val();

        $.ajax({
            type: 'POST',
            url: '{{ route('Lms.Econtent.topic') }}',
            data: {
                _token: '{{ csrf_token() }}',
                
                subject_id:sub,
                faculty_id: fac,
                semester_id: sem,
                chapter_no_id: id,
                //subjct_id:subject,
            },
            'beforeSend': function (request) {
                    if ($this){
                       $('#data').empty();
                    }
                },
            success: function (response){
                var data = $.parseJSON(response);
                if (data.error) {
                    $.notify(data.message, "warning");
                } else {
                    $.each(data.topic, function(key,valueObj){
                        var active_selected="";
                        var inactive_selected="";
                        if(valueObj.status==1){
                            active_selected="selected";
                               //alert(active_selected);
                        }
                        else if(valueObj.status==0){
                            inactive_selected="selected";
                               //alert(inactive_selected);
                        }

                        
                        $('#data').append("<tr><td class='center first-child'><label class='pos-rel'><input type='checkbox' name='chkIds[]' value="+valueObj.id+" class='ace' /><span class='lbl'></span>"+
                            "<td>"+ (i++) +
                            "<td>"+ valueObj.topic +
                            "<td>"+((valueObj.file)?'<a href="/Econtent/'+valueObj.file +'">'+valueObj.file+'</a>':"-")+
                            "<td>"+valueObj.detail+
                            "<td>"+'<a href="Econtent/'+valueObj.id +'/edit" class="btn btn-minier btn-success bootbox-confirm"><i class="ace-icon fa fa-pencil bigger-130"></i></a>'+' &nbsp; '+'<a href="Econtent/'+valueObj.id+'/delete" class="btn btn-primary btn-minier btn-danger "><i class="ace-icon fa fa-trash-o bigger-130"></i></a>'+
                            "<td></tr>");
                    }); 
                }   
            }
        });
    }
    $(document).ready(function(){
        $(document).on('change','.changeStatus',function(e){
            e.preventDefault();
            var status= $(this).val();
            var id= $(this).attr('id'); 
            $.post(
                "{{route('Lms.Econtent.changeStatus')}}",
                {status:status , id : id, _token:'{{ csrf_token() }}'},
                function(response){
                    var data = $.parseJSON(response);
                    if(data.error){
                        toastr.warning(data.msg,"Warning"); 
                    }
                    else{
                        toastr.success(data.msg,"Success");
                       
                    }   
            });
        });
    });
</script>


    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    
    @include('includes.scripts.datepicker_script')

@endsection