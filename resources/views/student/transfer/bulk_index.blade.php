@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->
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
                            Student Promotion
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            <h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student</h4>
                                {{@Form::open(['route'=>'student.transfer.bulk','method'=>'get'])}}
                                <div class="clearfix">
                                    <div class="form-group">
                                        {!! Form::label('reg_no', 'REG. NO.', ['class' => 'col-sm-2 control-label']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration", "autofocus"]) !!}

                                            @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
                                        </div>

                                        {!! Form::label('reg_date', 'Reg. Date', ['class' => 'col-sm-2 control-label']) !!}
                                        <div class=" col-sm-5">
                                            <div class="input-group ">
                                                {!! Form::text('reg_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                                <span class="input-group-addon">
                                                    <i class="fa fa-exchange"></i>
                                                </span>
                                                {!! Form::text('reg_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                                @include('includes.form_fields_validation_message', ['name' => 'reg_start_date'])
                                                @include('includes.form_fields_validation_message', ['name' => 'reg_end_date'])
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">{{ env('course_label') }}</label>
                                        <div class="col-sm-2">
                                            {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);']) !!}

                                        </div>

                                        <label class="col-sm-1 control-label">{{env('section_label')}}</label>
                                        <div class="col-sm-2">
                                            {!! Form::select('semester',$semester_list,'', ['class'=>'form-control']) !!}
                                        </div>

                                        <label class="col-sm-1 control-label">Status</label>
                                        <div class="col-sm-2">
                                            <select class="form-control border-form" name="status" id="cat_id">
                                                <option value="all"> Select Status </option>
                                                <option value="active" >Active</option>
                                                <option value="in-active" >In-Active</option>
                                            </select>
                                        </div>
                                        <label class="col-sm-1 control-label">Session</label>
                                        <div class="col-md-2">{!! Form::select('session', $ssn_list,Session::get('activeSession'), ['class'=>'form-control']) !!}</div>
                                    </div>
                                </div>

                                <div class="clearfix form-actions">
                                    <div class="col-md-12 align-right">        &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                            <i class="fa fa-filter bigger-110"></i>
                                            Search
                                        </button>
                                    </div>
                                </div>
                                {{ @Form::close() }}
                            <div class="hr hr-18 dotted hr-double"></div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->

                <div class="row">
                    <div class="col-xs-12">
                        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
                        <div class="table-header">
                            {{ $panel }}  Record list on table. Filter list using search box as your Wish.
                        </div>
                        <div class="table-responsive">
                            {!!Form::open(['route'=>'student.transfer.bulk','method'=>'post','id'=>'bulk_promote'])!!}
                                <table id="dynamic-tableeeeeeeee" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                            <th><input type="checkbox" id="checkAll"></th>
                                            <th>S.N.</th>
                                            <th>Reg. Number</th>
                                            <th>Name of Student</th>
                                            <th>Father Name</th>
                                            <th>Current {{ env('course_label') }}</th>
                                            <th>Current Section</th>
                                            <th>Promoted In</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (isset($data['student']) && $data['student']->count() > 0)
                                            @php($i=1)
                                            @foreach($data['student'] as $student)
                                                <tr>
                                                    <td><input type="checkbox" name="ids[{{$student->id}}]" class="ids">
                                                        <input type="hidden" name="old_session" value="{{$_GET['session']}}">
                                                    </td>
                                                    <td>{{ $i }}</td>
                                                    <td><a href="{{ route($base_route.'.view', ['id' => $student->id]) }}">{{ $student->reg_no }}</a>
                                                        <span class="scholar_no" style="display:none;">{{ $student->id }}</span>
                                                        <span class="old_ssn" style="display:none;">{{ $student->old_ssn }}</span>
                                                    </td>
                                                    <td> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</td>
                                                    <td>{{$student->father_name}}</td>
                                                    <td> {{ ViewHelper::getFacultyTitle($student->faculty) }}</td>
                                                    <td> {{ ViewHelper::getSemesterTitle($student->semester) }}</td>
                                                    <td>
                                                        @if($student->promoted_course)
                                                            {{$student->promoted_course}}
                                                            <br>
                                                            {{$student->promoted_session}}
                                                            <br>
                                                            <b>{{$student->Status}}</b>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php($i++)
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8">No {{ $panel }} data found.</td>
                                                
                                            </tr>
                                        @endif
                                        </tbody>
                                </table>
                                <table class="table table-bordered table-striped table-hover">
                                    <tr>
                                        <th>Promote To {{env('course_label')}}</th>
                                        <th>Promote To Session</th>
                                        <th>Promote To Section</th>
                                        <th>Promote To Status</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td>{!! Form::select('course', $course_options,null,['class'=>'form-control course','required']) !!}</td>
                                        <td>{!! Form::select('session', $ssn_list,null,['class'=>'form-control sessn','required']) !!}</td>
                                        <td>{!! Form::select('semester', $semester_list,null,['class'=>'form-control smstr','required']) !!}</td>
                                        <td>
                                            <select name="status" class="form-control status" required="required">
                                                <option value="">--Select Status--</option>
                                                <option>Pass</option>
                                                <option>Fail</option>
                                                <option>Passout</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-success" type="submit" id="promote_btn"> Promote </button>
                                        </td>
                                    </tr>
                                </table>
                            {!! Form::close() !!}
                        </div>
                        
                    </div>
                </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        /*Change Field Value on Capital Letter When Keyup*/
            $( "#bulk_promote" ).submit(function() {
              $('#promote_btn').attr('disabled',true);
            });
            $("#checkAll").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
            });
        $(function() {
            $('.upper').keyup(function() {
                this.value = this.value.toUpperCase();
            });
            

        });

        $(document).ready(function () {

           
            $('#student-transfer-btn').click(function () {

                faculty = $('#transfer-faculty').val();
                semester = $('#transfer-semester').val();
                status = $('#transfer-status').val();

                if (faculty !== '' & faculty >0) {
                    if (semester !== '' & semester >0) {
                        if (status !== '' & status >0) {
                            /*Check Student List Select Or not*/
                            $chkIds = document.getElementsByName('chkIds[]');
                            var $chkCount = 0;
                            $length = $chkIds.length;

                            for (var $i = 0; $i < $length; $i++) {
                                if ($chkIds[$i].type == 'checkbox' && $chkIds[$i].checked) {
                                    $chkCount++;
                                }
                            }

                            if ($chkCount <= 0) {
                                toastr.info("Please, Select At Least One Student Record.", "Info:");
                                return false;
                            }

                        }else{
                            toastr.info("Please, Select Correct Student Status.", "Info:");
                            return false;
                        }
                    }else{
                        toastr.info("Please, Select Your Target Section.", "Info:");
                        return false;
                    }
                }else{
                    toastr.info("Please, Select Your Target Faculty.", "Info:");
                    return false;
                }

            });


        });

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
                        $('.semester_select').html('').append('<option value="0">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('.semester_select').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });

        }


    </script>
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.datepicker_script')

    @endsection