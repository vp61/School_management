<h4 class="header light lighter blue"><i class="fa fa-list"></i>
    Blocks List  
</h4>
<div class="table-header hidden-print">
    Blocks Record list on table. Filter list using search box as your Wish.
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
                    <th>Edit</th>
                </tr>
                </thead>
                <tbody>
                  
                @if (isset($data['blocks']) && $data['blocks']->count() > 0)
                    @php($i=1)
                    @foreach($data['blocks'] as $blocks)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $blocks->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td>{{$blocks->hostel}}</td>
                            <td>
                                {{ $blocks->title }}
                            </td>
                            
                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <a class="green" href="/hostel/{{$id}}/block/{{ $blocks->id }}/edit" title="EDIT BLOCK">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>
                               
                                    <a class="red" href="/hostel/{{$id}}/block/{{ $blocks->id }}/delete" title="DELETE BLOCK">
                                        <i class="ace-icon fa fa-trash bigger-130"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No block found.</td>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>