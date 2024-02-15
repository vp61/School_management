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
                       {{$panel}}  Manager
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Transfer {{$data['student'][0]['first_name']}} 
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        <!-- PAGE CONTENT BEGINS -->
             <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Current Data</h4>
                <div class="table-responsive">
                    <table id="dynamic-tableeeeeeeee" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Reg. Number</th>
                                    <th>Name of Student</th>
                                    <th>Current Branch</th>
                                    <th>Current Course</th>
                                    <th>Current Semester</th>
                                </tr>
                             </thead>
                             <tbody>
                                 <tr>
                                     <td><a href="{{ route($base_route.'.view', ['id' => $data['student'][0]['id']]) }}">{{ $data['student'][0]['reg_no'] }}</a></td>
                                    <td> {{ $data['student'][0]['first_name'] }}</td>
                                    <td>{{ViewHelper::getBranchTitle(Session::get('activeBranch'))}}</td>
                                    <td> {{ ViewHelper::getFacultyTitle($data['student'][0]['course_id']) }}</td>
                                    <td> {{ ViewHelper::getSemesterTitle($data['student'][0]['Semester']) }}</td>
                                 </tr>
                             </tbody> 
                         </table> 
                         <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Transfer To</h4>
                         {{@Form::open(['route'=>$base_route.'.transfering-student'])}} 
                         <div class="table-responsive">
                    <table id="dynamic-tableeeeeeeee" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Select Branch</th>
                                    <th>Select Session</th>
                                    <th>Select Course</th>
                                    <th>Select Semester</th>
                                </tr>
                             </thead>
                             <tbody>
                                 <tr>
                                    <input type="hidden" name="student" value='{{$id}}'>
                                    <input type="hidden" name="student_session" value='{{$session}}'>
                                        <td>{{Form::select('branch',$data['branches'],null,['class'=>'form-control','onchange'=>'loadCourse(this)','id'=>'branch','required'=>'required'])}}</td>
                                        <td>{!! Form::select('session', $ssn_list, '',['class'=>'form-control ','id'=>'session','onchange'=>'loadCourse(this)','required'=>'required']) !!}</td>
                                        <td>{!! Form::select('course', [''=>'select'],'',['class'=>"form-control",'onchange'=>'loadSemesters(this)','id'=>'course','required'=>'required']) !!}</td>

                                        <td>{!! Form::select('semester', [''=>'select'], '',['class'=>"form-control ",'id'=>'semester','onchange'=>'getFee()','required'=>'required']) !!}</td>
                                 </tr>
                             </tbody> 
                         </table> 
                         <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Fee Data</h4>    <div class="table-responsive">
                            <table id="dynamic-tableeeeeeeee" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Current Assigned</th>
                                            <th>Assign To</th>
                                        </tr>
                                     </thead>
                                     <tbody>
                                         
                                            @foreach($old_assign_fee as $akey=>$value)
                                            <tr id="">
                                                <td>{{$value->fee_head_title}} ({{$value->times}})(Amount:- &#8377; {{$value->fee_amount}})<br><b>PAID:-</b>
                                                    @foreach($old_collect_fee as $key=>$val)
                                                        @foreach($val as $k=>$v)
                                                            @if($key==$value->id)
                                                           &#8377;  {{$v->amount_paid}}
                                                        @endif
                                                        @endforeach
                                                    @endforeach
                                                </td>
                                                <td class="" id="">{!! Form::select("assign[$value->id]", [''=>'select'], '',['class'=>"form-control transfer",'required'=>'required']) !!}</td>
                                            </tr>    
                                            @endforeach
                                         
                                     </tbody>
                             </table>    
                             <div class="clearfix form-actions">
                                <div class="col-md-12 align-right">        &nbsp; &nbsp; &nbsp;
                                    <button class="btn btn-info" type="submit" id="filter-btn">
                                        <i class="fa fa-filter bigger-110"></i>
                                        Transfer
                                    </button>
                                </div>
                            </div> 
                            {{@Form::close()}} 
                    </div><!-- /.col -->
                </div><!-- /.row -->

                    
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        /*Change Field Value on Capital Letter When Keyup*/
        $(function() {
            $('.upper').keyup(function() {
                this.value = this.value.toUpperCase();
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
                        toastr.warning(data.message, "warning");
                    } else {
                        $('#semester').html('').append('<option value="">--Select Semester--</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('#semester').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });

        }
        function loadCourse($this) {
            var branch=document.getElementById('branch');
            var session=document.getElementById('session');
            $('#semester').prop('selectedIndex'," ");
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-course') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    branch_id: branch.value,
                    session_id: session.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                      toastr.warning(data.message, "warning");
                    } else {
                        $('#course').html('').append('<option value="">--Select Course--</option>');
                        $.each(data.course, function(key,valueObj){
                            $('#course').append('<option value="'+valueObj.id+'">'+valueObj.faculty+'</option>');
                        });
                    }
                }
            });

        }
        function getFee(){
               var branch=document.getElementById('branch');
               var session=document.getElementById('session');
               var course=document.getElementById('course');
               var section=document.getElementById('semester');

               $.ajax({
                type: 'POST',
                url: '{{ route('student.find-assigned-fee') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    branch_id: branch.value,
                    session_id: session.value,
                    course_id:course.value,
                    section_id:section.value
                },
                success: function(response){
                   var data = $.parseJSON(response);
                    if (data.error) {
                        toastr.warning(data.message, "warning");
                    } else {
                        $('.transfer').html('').append('<option value="">--Select Head--</option>');
                        
                          $.each(data.data, function(key,valueObj){
                            $('.transfer').append('<option value="'+valueObj.id+'">'+valueObj.fee_head_title+'('+valueObj.fee_amount+')</option>');
                        });
                        
                    } 
                }
               });

              
        }

    </script>
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
  {{--  @include('includes.scripts.dataTable_scripts') --}}
    @include('includes.scripts.datepicker_script')

    @endsection