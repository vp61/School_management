@include('includes.data_table_header')
<table id="dynamic-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Term</th>
                    <th>Exam Type</th>
                    <th>{{env('course_label')}}</th>
                    <th>Section</th>
                    <th>Subject</th>
                    <th>Maximum Mark</th>
                    <th>Passing Mark</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($dropdowns['exam']) && $dropdowns['exam']->count() > 0)
                    @php($i=1)
                    @foreach($dropdowns['exam'] as $exam)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $exam->exam_title }}</td>
                            <td>{{ $exam->exam_description }}</td>
                            <td>{{ $exam->term }}</td>
                            <td>{{ $exam->type }}</td>
                            <td>{{ $exam->faculty }}</td>
                            <td>{{ $exam->section }}</td>
                            <td>{{ $exam->subject }}</td>
                            <td>{{ $exam->max_mark }}</td>
                            <td>{{ $exam->pass_mark }}</td>
                            <td nowrap>
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <a class="blue" href="{{ route('exam.add-question', ['id' => $exam->id]) }}" title="Add Question">
                                        <i class="ace-icon fa fa-plus bigger-130"></i>
                                    </a>
                                    <a class="green" href="{{ route($base_route.'.edit', ['id' => $exam->id]) }}" title="Edit">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => $exam->id]) }}" class="red bootbox-confirm" title="Delete">
                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @endif
            </tbody>
        </table>