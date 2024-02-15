<h4 class="header light lighter blue"><i class="fa fa-list"></i>
    Floors List  
</h4>
<div class="table-header hidden-print">
    Floors Record list on table. Filter list using search box as your Wish.
</div>
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
                    <th>Hostel</th>
                    <th>Block</th>
                    <th>Floor</th>
                    <th>Edit</th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['floor']) && $data['floor']->count()>0)
                    @php($i=1)
                    @foreach($data['floor'] as $floor)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td>
                                {{$floor->hostel}}
                            </td>
                            <td>
                                {{$floor->block}}
                            </td>
                            <td>
                                {{$floor->floor}}
                            </td>    
                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <a class="green" href="/hostel/{{$id}}/floor/{{$floor->id}}/edit" title="EDIT FLOOR">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>
                                    <a class="red" href="/hostel/{{$id}}/floor/delete/{{$floor->id}}" title="DELETE FLOOR">
                                        <i class="ace-icon fa fa-trash bigger-130"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="6">No floor found.</td>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>