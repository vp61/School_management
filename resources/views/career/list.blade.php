@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.dataTables.min.css') }}" />
   <!--  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" type="text/css"  /> -->
  <!--  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css" type="text/css" /> -->
   
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        @php($panel='Career')
                       Career
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            List
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <div class="row">
                   
                    <div class="col-xs-12 ">
                    
                      
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        {!! Form::open(['route' => 'career.list', 'method' => 'GET', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        <div class='form-group'>
                            {!!Form::label('date','DATE',['class'=>'control-label col-sm-1'])!!}
                            <div class=" col-sm-5">
                                <div class="input-group ">
                                    {!! Form::date('from', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                    <span class="input-group-addon">
                                        <i class="fa fa-exchange"></i>
                                    </span>
                                    {!! Form::date('to', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                </div>
                            </div>
                            {!!Form::label('Type','Type',['class'=>'control-label col-sm-1'])!!}
                            <div class="col-sm-3"> 
                              {!! Form::select('type',['' => '--Select--','1'=>'Teaching','2'=>'Non-Teaching'],null, [ "class" => "form-control border-form upper"]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                          <!--  subject change-->
                           {!!Form::label('Subject','Subject',['class'=>'control-label col-sm-1'])!!}
                            <div class="col-sm-3"> 
                              {!! Form::select('subject',['' => '--Select subject--','Hindi'=>'Hindi','English'=>'English','Math'=>'Math','Science'=>'Science','Social Science'=>'Social Science','Computer Science'=>'Computer Science','Physics'=>'Physics','Chemistry'=>'Chemistry','Biology'=>'Biology','Mathematics'=>'Mathematics','History'=>'History','Geography'=>'Geography','Political Science'=>'Political Science','Psychology cum School counsellor'=>'Psychology cum School counsellor','Accountancy'=>'Accountancy','Business Studies'=>'Business Studies','Economics'=>'Economics'],null, [ "class" => "form-control border-form upper"]) !!}
                            </div>
                             <!--  subject change-->
                            <div class="col-md-12 align-right">        &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-info" type="submit" id="filter-btn">
                                <i class="fa fa-filter bigger-110"></i>
                                Search
                            </button>
                        </div>
                        </div>
                        {!! Form::close() !!}
                        <!-- PAGE CONTENT BEGINS -->
                           
                       
                    </div><!-- /.col -->
                    
                    <div class='col-sm-12'>
                       @include('includes.data_table_header')

                        <div class="table-responsive">
                           <table class='table' id="dynamic-table1">
                               <thead>
                                   <tr>
                                       <th>S.No</th>
                                       <th class="noExport">Action</th>
                                       <th>Current Status</th>
                                       <th>Type</th>
                                       <th>Candidate Name</th>
                                       <th>Father's Name</th>
                                       <th>Email</th>
                                       <th>Gender</th>
                                       <th>DOB</th>
                                       <th>Mobile</th>
                                       <th>Address</th>
                                       <th>Mother Teacher</th>
                                       <th>Experience</th>
                                       <th>PRT</th>
                                       <th>TGT</th>
                                       <th>PGT</th>
                                       <th>NTT</th>
                                       <th>Graduation</th>
                                       <th>Graduation Subject</th>
                                       <th>Graduation (%)</th>
                                       <th>PG</th>
                                       <th>PG Pursue Year</th>
                                       <th>PG(is complete)</th>
                                       <th>PG (Subject)</th>
                                       <th>PG (%)</th>
                                       <th>B.Ed. Pursue Year</th>
                                       <th>B.Ed.(Is Complete)</th>
                                       <th>M.Ed. Pursue Year</th>
                                       <th>M.Ed.(Is Complete)</th>
                                       <th>12Th Stream</th>
                                       <th>12Th (%)</th>
                                       <th>10Th (%)</th>
                                       <th>10Th Board</th>
                                       <th>Year Of Experience</th>
                                       <th>Present Organization</th>
                                       <th>Presently Teaching(Class)</th>
                                       <th>Language Known</th>
                                       <th>Qualification</th>
                                       <th>Applied For</th>
                                       <th>Current Salary</th>
                                       <th>Expected Salary</th>
                                       <th>Leaving Reason</th>
                                       <th>Expected Joining</th>
                                       <th>Applied On</th>
                                       
                                       
                                      
                                   </tr>
                               </thead>
                               @php($i=1)
                               @foreach($list as $k => $v)
                               <?php
                                  $tenth_percent= '10_percentage';
                                  $twelve_percent= '12_percentage';
                                  $twelve_stream= '12_stream';


                                ?>
                                   <tr>
                                       <td>{{$i}}</td>
                                       <td><a class="green btn btn-minier btn-success" href="{{ route('career.view', ['id' => $v->id]) }}">
                                                <i class="ace-icon fa fa-eye bigger-130"></i>
                                           </a>
                                           <a class="green btn btn-minier btn-primary" title="add followup" href="{{ route('career.followup', ['id' => $v->id]) }}">
                                                <i class="ace-icon fa fa-phone bigger-130"></i>
                                           </a>
                                          
                                            <a  title='Delete career' href="{{ route('career.delete', ['id' => $v->id]) }}" class="red bootbox-confirm btn btn-minier btn-danger">
                                                <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                            </a>
                                        </td>
                                        <td>
                                        {{$v->careerstatus}}
                                      </td>
                                      <td> @if($v->type==1)
                                        Teaching
                                        @else
                                        Non-Teaching
                                        @endif</td>
                                       <td>{{$v->candidate_name}}</td>
                                       <td>{{$v->father_name}}</td>
                                       <td>{{$v->email}}</td>
                                       <td>{{$v->gender}}</td>
                                       <td>{{$v->dob}}</td>
                                       <td>{{$v->mobile}}</td>
                                       <td>{{$v->per_add}}</td>
                                       <td>{{$v->mother_teacher}}</td>
                                       <td>{{$v->experience}}</td>
                                       <td>{{$v->prt}}</td>
                                       <td>{{$v->tgt}}</td>
                                       <td>{{$v->pgt}}</td>
                                       <td>{{$v->ntt}}</td>
                                       <td>{{$v->graduation}}</td>
                                       <td>{{$v->graduation_subject}}</td>
                                       <td>{{$v->graduation_percentage}}</td>
                                       <td>{{$v->post_graduation}}</td>
                                       <td>{{$v->post_graduation_pursuing_year}}</td>
                                       <td>{{$v->is_pg_completed}}</td>
                                       <td>{{$v->post_graduation_subject}}</td>
                                       <td>{{$v->post_graduation_percentage}}</td>
                                       <td>{{$v->b_ed_pursuing_year}}</td>
                                       <td>{{$v->is_b_ed_completed}}</td>
                                       <td>{{$v->m_ed_pursuing_year}}</td>
                                       <td>{{$v->is_m_ed_completed}}</td>
                                       <td>{{$v->$twelve_stream}}</td>
                                       <td>{{$v->$twelve_percent}}</td>
                                       <td>{{$v->$tenth_percent}}</td>
                                       <td>{{$v->board}}</td>
                                       <td>{{$v->year_of_experience}}</td>
                                       <td>{{$v->pesent_organization}}</td>
                                       <td>{{$v->classes_presently_teaching}}</td>
                                       <td>{{$v->languages_known}}</td>
                                       <td>{{$v->qualification}}</td>
                                       <td>{{$v->post_applied_for}}</td>
                                       <td>{{$v->current_salary}}</td>
                                       <td>{{$v->expected_salary}}</td>
                                       <td>{{$v->leaving_reason}}</td>
                                       <td>{{$v->join_day}}</td>
                                       <td>{{\Carbon\Carbon::parse($v->created_at)->format('d-M-Y')}}</td>
                                    
                                      
                                       
                                   </tr>
                               @php($i++)
                               @endforeach
                               
                           </table>
                        </div>
                    </div>
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
<script>
  $('.careere_select_drop').change(function(){
    var id = $(this).attr('id');
    var status = $(this).val();
    window.location.href = "/career/"+id+"/"+status+"/ChangeStatus";
  })


$(document).ready(function() {
    $('#dynamic-table1').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                exportOptions: {
                    columns: [ 2, ':visible' ]
                }
            },
            
            {
                extend: 'csv',
                exportOptions: {
                    columns: [ 0,1,2, 3,4,5, 6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,43]
                }
            },
            'colvis'
        ]
    } );
} );


</script>
    
   
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
@endsection