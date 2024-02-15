<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Lesson plan List</h4>
        <div class="clearfix">

    <span class="easy-link-menu">
        <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="active"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;Active</a>
        <a class="btn-warning btn-sm bulk-action-btn" attr-action-type="in-active"><i class="fa fa-remove" aria-hidden="true"></i>&nbsp;In-Active</a>
        <a class="btn-danger btn-sm bulk-action-btn" attr-action-type="delete"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;Delete</a>
    </span>

            <span class="pull-right tableTools-container"></span>
        </div>
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
                    <th>Faculty</th>
                    <th>SEM/SEC</th>
                    <th>subject</th>
                    <th>Chapter</th>
                    <th>Topic</th>
                    <th>file</th>
                    <th>From Date</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>  
                @if (isset($data['Lesson_plans']) && $data['Lesson_plans']->count() > 0)
                    @php($i=1)
                    @foreach($data['Lesson_plans'] as $Lesson_plans)
                        
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $Lesson_plans->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td>{{ $Lesson_plans->faculty }}</td>
                            <td>{{ $Lesson_plans->semester }}</td>
                           
                           
                            <td>
                               {{ $Lesson_plans->subject }}
                            </td>
                             <td>
                               {{ $Lesson_plans->title }}
                            </td>
                             <td>{{$Lesson_plans->topic}}</td>
                            <td>
                                <a href="{{ asset('Lesson_plans'.DIRECTORY_SEPARATOR.$Lesson_plans->file) }}" target="_blank">
                                           {{ $Lesson_plans->file }}
                                </a>
                            </td>
                            <td>
                                @if($Lesson_plans->publish_date)
                                    {{\Carbon\Carbon::parse($Lesson_plans->publish_date)->format('d-M-Y')}}
                                @endif
                            </td>
                            <td>
                                {{ $Lesson_plans->created_by }}
                            </td>
                            <td>
                                    
                                {!! Form::select('status',$data['status'],$Lesson_plans->st?$Lesson_plans->st:'' , ['class' => 'form-control  changeStatus','id'=>$Lesson_plans->id]) !!}
                                    
                            </td>
                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <a href="{{route($base_route.'.edit',[$Lesson_plans->id])}}" class="btn btn-primary btn-minier btn-success">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>
                                      
                                    <a href="{{route($base_route.'.delete',[$Lesson_plans->id])}}" class="btn btn-primary btn-minier btn-danger bootbox-confirm" >
                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="13">No {{ $panel }} data found.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        {!! Form::close() !!}
    </div>
</div>

