@include('includes.data_table_header')
<table id="dynamic-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Course</th>
                    <th>Result Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['result_type']) && $data['result_type']->count() > 0)
                    @php($i=1)
                    @foreach($data['result_type'] as $result)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $result->course }}</td>
                            <td><?php
                             if($result->result_type_id==1){
                                echo "class 1- 3";
                             }
                             if($result->result_type_id==2){
                                echo "class 4- 8";
                             }

                            ?></td>
                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <a class="green" href="/exam/setup/result-type/edit/{{$result->id}}" title="Edit">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => $result->id]) }}" class="red bootbox-confirm" title="Delete">
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