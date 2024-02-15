@include('includes.data_table_header')
<table id="dynamic-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Course / Class</th>
                    <th>Head</th>
                    <th>Due Month</th>
                    <th>Start Date</th>
                    <th>Daily Fine</th>
                    <th>Monthy Fine</th>
                    <th>Entered By</th>
                    <th>Created At</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($dropdown['list']) && $dropdown['list']->count() > 0)
                    @php($i=1)
                    @foreach($dropdown['list'] as $list)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $list->faculty }}</td>
                            <td>{{ $list->fee_head_title }}</td>
                            <td>{{ $list->month }}</td>
                            <td>{{ \Carbon\Carbon::parse($list->start_date)->format('d-m-Y') }}</td>
                            <td>{{ $list->daily_fine }}</td>
                            <td>{{ $list->monthly_fine }}</td>
                            <td>{{ $list->entered_by }}</td>
                            <td>{{ \Carbon\Carbon::parse($list->created_at)->format('d-m-Y') }}</td>
                            <td>
                                
                                <a href="{{ route($base_route.'.delete', ['id'=>$list->id]) }}" class="red bootbox-confirm" title="Delete">
                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                    </a>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @endif
            </tbody>
        </table>