@php $panel="Student"; @endphp
<div class="row">
    <div class="col-xs-12">
        @include('includes.data_table_header')
        <!-- div.table-responsive -->
            <!-- div.table-responsive -->
        <div class="table-responsive">

            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">

                <thead>
                <tr>
                    <th class="center">
                        <label class="pos-rel">
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <th>S.N.</th>
                    <th>Course</th>
                    {{-- <th>Sem.</th> --}}
                    <th>Reg.Date</th>
                    <th>Reg.Num</th>
                    <th>Student Name</th>
                    <th>Father Name</th>
                    <th>Mobile</th>
                    <th>Indose No</th>
                    <th>Status</th>
                    <th>Edit/Due Report</th>
                </tr>
                </thead>
                <tbody>

                @if (isset($data['student']) && $data['student']->count() > 0)
                    @php($i=1)
                    @foreach($data['student'] as $student)

                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $student->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>

                            <td> {{  ViewHelper::getFacultyTitle( $student->faculty ) }}</td>
                            {{-- <td> {{  ViewHelper::getSemesterTitle( $student->semester ) }}</td> --}}
                            
                            <td>{{ \Carbon\Carbon::parse($student->reg_date)->format('Y-m-d')}} </td>
                            <td><a href="{{ route($base_route.'.view', ['id' => $student->id]) }}">{{ $student->reg_no }}</a></td>
                            <td><a href="{{ route($base_route.'.view', ['id' => $student->id]) }}"> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</a></td>
                            
                           <td>{{ $student->father_name }}</td> 
                           <td>{{ $student->mobile_1 }}</td> 
                           <td>{{$student->indose_number}}</td>
                            <td class="hidden-480 ">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary btn-minier dropdown-toggle {{ $student->status == 'active'?"btn-warning":"btn-info" }}" > 
                                        {{ $student->status == 'in-active'?"In Active":" Active" }}
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
                        <td colspan="11">No {{ $panel }} data found.</td>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            {!! Form::close() !!}

        </div>
    </div>
</div>