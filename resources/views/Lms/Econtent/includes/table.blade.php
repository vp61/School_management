
<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} filter</h4>
        <div class="clearfix">
            <span class="pull-right tableTools-container"></span>
        </div>
        <div class="table-header">
            {{ $panel }}  Record list on table. Filter list using search box as your Wish.
        </div>
       <div class="row" style="border:0px solid black; border-radius:20px; padding:5px 0px;">
            <div class="col-sm-3" style="max-height: 500px;overflow: scroll;">
                <ul class="list-group">
                    <li class="list-group-item  active">Choose From Listed:</li>
                        @foreach($data['chapter'] as $key=>$value)
                             <input type="hidden" name="subjects_id" class="subjects_id_{{$value->chapter_no_id}}" value="{{$key}}">
                             <input type="hidden" name="faculty_id" class="faculty_id_{{$value->chapter_no_id}}" value="{{$value->faculty_id}}">
                             <input type="hidden" name="section_id" class="section_id_{{$value->chapter_no_id}}" value="{{$value->semesters_id}}">
                            <li class="list-group-item" onclick="showtopic(this)" style="cursor: pointer;" id="{{$value->chapter_no_id}}">
                                <b style="font-size: 10px">Chapter - {{$value->title}} <br> {{env('course_label')}}- {{$value->faculty}} ( {{$value->semester}}- {{$value->subject}}) </b>
                                <i class="fa fa-plus icon{{$value->chapter_no_id}} pull-right"  ></i>
                                
                            </li>
                        @endforeach
                </ul>
            </div>
            <div class="col-sm-9">
                <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
                <div class="table-header">
                    {{ $panel }}  Record list on table. Filter list using search box as your Wish.
                </div>
                <!-- div.table-responsive -->
                <div class="table-responsive">
                    {!! Form::open(['route' => 'assignment.bulk-action', 'id' => 'bulk_action_form']) !!}
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
                            <th>Topic</th>
                            <th>file</th>
                            <th>detail</th>
                            <th colspan="2">Action</th>
                        </tr>
                        </thead>
                        <tbody id="data">
                        </tbody>
                    </table>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

