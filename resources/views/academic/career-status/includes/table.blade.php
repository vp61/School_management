
        @include('includes.data_table_header')
        <!-- div.table-responsive -->
        <div class="table-responsive">
           

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
                            <th>{{ $panel }}</th>
                           
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data['career-status']) && $data['career-status']->count() > 0)
                            @php($i=1)
                            @foreach($data['career-status'] as $careerstatus)
                                <tr>
                                    <td class="center first-child">
                                        <label>
                                            <input type="checkbox" name="chkIds[]" value="{{ $careerstatus->id }}" class="ace" />
                                            <span class="lbl"></span>
                                        </label>
                                    </td>
                                    <td>{{ $i }}</td>
                                    <td>{{ $careerstatus->title }}</td>
                                   
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons">
                                            <a class="green" href="{{ route($base_route.'.edit', ['id' => $careerstatus->id]) }}">
                                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                                            </a>

                                            <a href="{{ route($base_route.'.delete', ['id' => $careerstatus->id]) }}" class="red bootbox-confirm">
                                                <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                            </a>
                                        </div>
                                        <div class="hidden-md hidden-lg">
                                            <div class="inline pos-rel">
                                                <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                                    <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                                    <li>
                                                        <a href="{{ route($base_route.'.edit', ['id' => $careerstatus->id]) }}" class="tooltip-success" data-rel="tooltip" title="Edit">
                                                            <span class="green">
                                                                <i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
                                                            </span>
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="{{ route($base_route.'.delete', ['id' => $careerstatus->id]) }}" class="tooltip-error bootbox-confirm" data-rel="tooltip" title="Delete">
                                                            <span class="red ">
                                                                <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @php($i++)
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">No {{ $panel }} data found.</td>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
           
        </div>