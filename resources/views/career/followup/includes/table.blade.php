
        @include('includes.data_table_header')
        <!-- div.table-responsive -->
        <div class="table-responsive">
           

                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                         
                            <th>S.N.</th>
                            <th>Followup Date</th>
                            <th> Next Followup Date</th>
                            <th>Response</th>
                            <th>Status</th>
                           
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data['followup']) && $data['followup']->count() > 0)
                            @php($i=1)
                            @foreach($data['followup'] as $followup)
                                <tr>
                        
                                    <td>{{ $i }}</td>
                                    <td>{{carbon\carbon::parse($followup->followup_date)->format('d-m-Y')}}</td>
                                    <td>{{!empty($followup->next_followup_date)?carbon\carbon::parse($followup->next_followup_date)->format('d-m-Y'):''}}</td>
                                    <td>{{ $followup->response }}</td>
                                    <td>{{ $followup->careerStatus }}</td>
                                   
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons">
                                            

                                            <a href="{{ route($base_route.'.followup.delete', ['id' => $followup->id]) }}" class="red bootbox-confirm">
                                                <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                            </a>
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