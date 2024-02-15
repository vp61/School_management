
  
@include('includes.data_table_header')                 
 <!-- bulk exam status -->                   
{!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}  
<!-- bulk exam status -->    
<table id="dynamic-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <!-- bulk exam status -->
                    <th class="center">
                        <label class="pos-rel">
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <!-- bulk exam status -->
                    <th>S.N.</th>
                    <th>Title</th>
                    <th>Mode</th>
                    <th>Term</th>
                    <th>Exam Type</th>
                    <th>{{env('course_label')}}</th>
                    <th>Section</th>
                    <th>Subject</th>
                    <th nowrap>Exam Date</th>
                    <th>Publish Status</th>
                    <th>Result Status</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['exam']) && $data['exam']->count() > 0)
                    @php($i=1)
                    @foreach($data['exam'] as $exam)
                        <tr>
                            <!-- bulk exam status -->
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $exam->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <!-- bulk exam status -->
                            <td>{{ $i }}</td>
                            <td>{{ $exam->exam_title }}</td>
                            <td>{{ $exam->mode }}</td>
                            <td>{{ $exam->term }}</td>
                            <td>{{ $exam->type }}</td>
                            <td>{{ $exam->faculty }}</td>
                            <td>{{ $exam->section }}</td>
                            <td>{{ $exam->subject }}</td>
                            <td nowrap>{{ $exam->date?\Carbon\Carbon::parse($exam->date)->format('d-M-Y'):'-' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary btn-minier dropdown-toggle {{ $exam->publish_status == 0?"btn-warning":"btn-info" }}" > 
                                        {{ $exam->publish_status == 0?"Pending":" Published" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('exam.create.status', ['id' => $exam->id,'status'=>1]) }}" title="Publish"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route('exam.create.status', ['id' => $exam->id,'status'=>0]) }}" title="Un-publish"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary btn-minier dropdown-toggle {{ $exam->result_status == 0?"btn-warning":"btn-info" }}" > 
                                        {{ $exam->result_status == 0?"Pending":" Published" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('exam.create.result_status', ['id' => $exam->id,'status'=>1]) }}" title="Publish"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route('exam.create.result_status', ['id' => $exam->id,'status'=>0]) }}" title="Un-publish"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td nowrap>
                                <div class=" action-buttons">
                                    <a class="tooltip-success btn btn-minier btn-success" data-rel="tooltip" data-original-title="Edit" href="{{ route('exam.add-question', ['id' => $exam->id]) }}" title="Add Question">
                                        <i class="ace-icon fa fa-plus"></i>
                                    </a>
                                    <a href="{{ route($base_route.'.edit', ['id' => $exam->id]) }}" title="Edit" class="tooltip-warning btn btn-minier btn-warning" data-rel="tooltip"><i class="fa fa-pencil"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => $exam->id]) }}"  title="Delete"class="tooltip-error btn btn-minier btn-danger bootbox-confirm" data-rel="tooltip">
                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @endif
            </tbody>
            <!-- bulk exam status -->
            <tfoot>
                <tr>
                    <td colspan="4" align="right"><a class="btn-primary btn-sm bulk-action-btn" attr-action-type="publish" aria-hidden="true"> Bulk Publish status</i></a>
                   
                        <a class="btn-warning btn-sm bulk-action-btn" attr-action-type="result" aria-hidden="true"> Bulk Result status</i></a>
                    </td>
                </tr>
            </tfoot>
        </table>
         {!! Form::close() !!}
        <!-- bulk exam status -->