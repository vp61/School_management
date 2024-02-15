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
                            Student Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                  
                    <div class="col-xs-12 ">
                  
                        <hr class="hr-6">
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        @include($view_path.'.includes.search_form')
                        <!-- PAGE CONTENT BEGINS -->
                         @include('includes.data_table_header')
                         <div class="table-responsive">
                        {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead >
                                <tr>
                                    <th class="center">
                                        <label class="pos-rel">
                                            <input type="checkbox" class="ace" />
                                            <span class="lbl"></span>
                                        </label>
                                    </th>
                                    <th>S.N.</th>
                                    <th>Student Reg No.</th>
                                    <th>{{env('course_label')}}</th>
                                    <th>Section</th>
                                    <th>Student Name</th>
                                    <th>Father Name</th>
                                    <th>Mobile</th>
                                    <th>Login(Email)</th>
                                    <th>Password</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                            @if (isset($data['user']) && $data['user']->count() > 0)
                                @php($i=1)
                                @foreach($data['user'] as $user)
                                    <tr>
                                        <td class="center first-child">
                                            <label>
                                                <input type="checkbox" name="chkIds[]" value="{{ $user->id }}" class="ace" />
                                                <span class="lbl"></span>
                                            </label>
                                        </td> 
                                        </td>
                                        <td>{{ $i }}</td>
                                        <td>{{ $user->reg_no }} </td>
                                        <td>{{ $user->faculty }} </td> 
                                        <td>{{ $user->semester }} </td> 
                                        <td>{{ $user->name }} </td>
                                        <td>{{ $user->father_first_name }} </td> 
                                        <td>{{ $user->mobile }} </td>  
                                        <td>{{ $user->email }} </td>
                                        <td>{{ $user->pass_Text }} </td>
                                       
                                       
                                    </tr>
                                    @php($i++)
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10">No {{ $panel }} data found.</td>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                     
                    </div>
                       {!! Form::close() !!}
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
  
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        $(document).ready(function () {

            /*Change Field Value on Capital Letter When Keyup*/
            $(function() {
                $('.upper').keyup(function() {
                    this.value = this.value.toUpperCase();
                });
            });
            /*end capital function*/

            $('#filter-btn').click(function () {

                var url = '{{ $data['url'] }}';
                var flag = false;
                var faculty = $('select[name="faculty"]').val();
              
                var status = $('select[name="status"]').val();

                if (faculty !== '') {
                    url += '?faculty=' + faculty;
                    flag = true;
                }else{
                    toastr.warning('Please Select Type', 'Warning:')
                    return false;
                }

               

                if (status !== '' ) {

                    if (status !== 'all') {

                        if (flag) {

                            url += '&status=' + status;

                        } else {

                            url += '?status=' + status;

                        }

                    }
                }

                location.href = url;

            });


        });

    </script>
@endsection