@include('includes.data_table_header')
<table id="dynamic-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['exam-term']) && $data['exam-term']->count() > 0)
                    @php($i=1)
                    @foreach($data['exam-term'] as $exam)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $exam->title }}</td>
                            <td>{{ $exam->description }}</td>
                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
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
                @else
                    <tr>
                        <td colspan="4">No {{ $panel }} data found.</td>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>