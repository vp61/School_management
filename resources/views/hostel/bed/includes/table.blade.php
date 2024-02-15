<h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
<!-- <div class="clearfix hidden-print">
    <span>
        {{--<a class="btn-primary btn-sm bulk-action-btn" attr-action-type="active"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;Active</a>
        <a class="btn-warning btn-sm bulk-action-btn" attr-action-type="in-active"><i class="fa fa-remove" aria-hidden="true"></i>&nbsp;In-Active</a>--}}
        <a class="btn-danger btn-sm bulk-action-btn" attr-action-type="delete"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;Delete</a>
        <a type="button" class="btn-primary btn-sm open-Addbeds" data-toggle="modal"
                data-target="#addbeds"
                data-hostel-id=""
                data-bed-id=""
                data-bed-number="" >
            <i class="fa fa-plus" aria-hidden="true"></i>&nbsp Add Beds
        </a>
    </span>
    <span class="pull-right tableTools-container"></span>
</div> -->
<div class="table-header hidden-print">
    {{ $panel }}  Record list on table. Filter list using search box as your Wish.
</div>
<div>
    {!! Form::open(['route' => 'hostel.bed.bulk-beds', 'id' => 'bulk_action_form']) !!}
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr >
                    <th class="center" width="5%" >
                        <label class="pos-rel">
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <th>Block</th>
                    <th>Floor</th>
                    <th>Room</th>
                    <th>Bed</th>
                    <th>Status</th>
                    <th>Edit</th>
                </tr>
                </thead>
                <tbody>
                    @if (isset($data['bed']) && $data['bed']->count() > 0)
                        @php($i=1)
                        @foreach($data['bed'] as $bed)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ $bed->id }}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $bed->block }}</td>
                                <td>{{ $bed->floor}}</td>
                                <td>{{ $bed->room}}</td>
                                <td>{{ $bed->bed_number}}</td>
                                <td>{{$bed->status}}</td>
                                <td>
                                    <div class="hidden-sm hidden-xs action-buttons">
                                    <a class="green" href="/hostel/{{$id}}/bed/{{ $bed->id }}/edit" title="EDIT BLOCK">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>
                               
                                    <a class="red" href="/hostel/{{$id}}/bed/{{ $bed->id }}/delete" title="DELETE BLOCK">
                                        <i class="ace-icon fa fa-trash bigger-130"></i>
                                    </a>
                                </div>
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">No {{ $panel }} data found.</td>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    {!! Form::close() !!}
</div>