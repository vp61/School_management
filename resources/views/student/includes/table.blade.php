@php $panel="Student"; @endphp
<div class="row">
    <div class="col-xs-12">
        @include('includes.data_table_header')
        <!-- div.table-responsive -->
            <!-- div.table-responsive -->
        <div class="table-responsive">

            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover" style="font-size: 11px !important">

                <thead>
                <tr>
                    <th class="center">
                        <label class="pos-rel">
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <th>S.N.</th>
                    <th>{{ env('course_label') }}</th>
                    @if(Session::get('isCourseBatch'))
                        <th>Batch</th>
                    @endif
                    <th>Sec/Sem</th> 
                    <th>Reg.Date</th>
                    <th>Adm. No.</th>
                    <th>Scholar No.</th>
                    <th>Student Name</th>
                    <th>D.O.B.</th>
                    <!--<th>Login Email</th>-->
                    <th>Father Name</th>
                    <th>Mother Name</th>
                    <th>Mobile</th>
                    <!-- <th>Aadhaar No</th>
                    <th>Category</th> -->
                    <th>Address</th>
                    <th>Status</th>
                    <th>Edit/Due Report</th>
                </tr>
                </thead>
                <tbody>

                @if (isset($data['student']) && $data['student']->count() > 0)
                    @php($i=1)
                   
                    @foreach($data['student'] as $student)
                        <tr >
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $student->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>

                            <td nowrap> {{  ViewHelper::getFacultyTitle( $student->faculty ) }}</td>
                            @if(Session::get('isCourseBatch'))
                                <td>{{$student->batch_title}}</td>
                            @endif
                             <td> {{  ViewHelper::getSemesterTitle( $student->semester ) }}</td> 
                            
                            <td nowrap>{{ \Carbon\Carbon::parse($student->reg_date)->format('d-M-Y')}} </td>
                            <td nowrap><a href="{{ route($base_route.'.view', ['id' => $student->id]) }}">{{ !empty($student->reg_no)?$student->reg_no:'-' }}</a></td>
                            <td nowrap><a href="{{ route($base_route.'.view', ['id' => $student->id]) }}">{{ !empty($student->university_reg)?$student->university_reg:'-' }}</a></td>
                            <td nowrap><a href="{{ route($base_route.'.view', ['id' => $student->id]) }}"> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</a></td>
                            <?php /*<td>{{ $student->login_email?$student->login_email:'-'}}</td> */?>
                            
                           <td nowrap>{{ \Carbon\Carbon::parse($student->dob)->format('d-M-Y')}}</td>
                           <td nowrap>{{ $student->father_name }}</td>
                           <td nowrap>{{ $student->mother_name }}</td> 
                           <td nowrap>{{ $student->mobile_1 }}</td> 
                           <?php /*<td nowrap>{{$student->aadhar_no}}</td>
                           <td nowrap>{{ $student->category_name }}</td> */?>
                           <td>{{ $student->address }}</td>
                            <td class="hidden-480 ">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary btn-minier dropdown-toggle {{ $student->status == 0?"btn-warning":"btn-info" }}" > 
                                        {{ $student->status == 0?"In Active":" Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route($base_route.'.active', ['id' => $student->id]) }}" title="Active"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route($base_route.'.in-active', ['id' => $student->id]) }}" title="In-Active"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td align="center">
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <!--a href="{{ route($base_route.'.view', ['id' => $student->id]) }}" class="btn btn-primary btn-minier btn-primary">
                                        <i class="ace-icon fa fa-eye bigger-130"></i>
                                    </a-->
 
                                    <a href="{{ route($base_route.'.edit', ['id' => $student->id]) }}" class="" title="Edit Record">
                                        <i class="ace-icon fa fa-edit bigger-130"></i>
                                    </a> 
                                    <a href="student/NoDue/{{$student->id}}" target="_blank" title="No Dues Report" ><i class=" ace-icon fa fa-bar-chart bigger-130" ></i></a>
                                    
                                    @ability('super-admin','account,school-leaving-certificate')
                                     <a href="{{ route($base_route.'.leave', ['id' => $student->id]) }}" class="" target="_blank" title="Print School Leaving Certificate">
                                        <i class="ace-icon fa fa-print bigger-130"></i>
                                    </a> 
                                    @endability
                                    
                                    @ability('super-admin','account,yearly-payment-report')
                                    <a href="{{ route($base_route.'.yearly_payment_report', ['id' => $student->id]) }}" target="_blank" class="" title="Yearly Payment Report"><i class="ace-icon fa fa-paypal bigger-130"></i></a> 
                                    @endability
                                    <!--a href="{{ route($base_route.'.delete', ['id' => $student->id]) }}" class="btn btn-primary btn-minier btn-danger bootbox-confirm" >
                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                    </a-->
                                </div>
                                <!--div class="hidden-md hidden-lg">
                                    <div class="inline pos-rel">
                                        <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                            <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                            <li><a href="{{ route($base_route.'.view', ['id' => $student->id]) }}" class="tooltip-success" data-rel="tooltip" title="Edit">
                                                   
                                                    <span class="blue">
                                                        <i class="ace-icon fa fa-eye bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route($base_route.'.edit', ['id' => $student->id]) }}" class="tooltip-success" data-rel="tooltip" title="Edit">
                                                    <span class="green">
                                                        <i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route($base_route.'.delete', ['id' => $student->id]) }}" class="tooltip-error bootbox-confirm" data-rel="tooltip" title="Delete">
                                                            <span class="red ">
                                                                <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                            </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div-->
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <?php  $span = Session::get('isCourseBatch')?15:14; ?>
                        <td colspan="{{$span}}">No {{ $panel }} data found.</td>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            {!! Form::close() !!}

        </div>
    </div>
</div>