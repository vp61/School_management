<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        <div class="table-header">
            {{ $panel }}  Record list on table. Filter list using search box as your Wish.
        </div>
            
                
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-tableeeeeeeee" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                        <!--th class="center"><label class="pos-rel">
                                
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span></label>
                            
                        </th-->
                        <th>S.N.</th>
                        <th>Reg. Number</th>
                        <th>Name of Student</th>
                        <th>Current Course</th>
                        <th>Current Semester</th>
                        <th>Promoted Course</th>
                        <th>Promoted Session</th>
                        <th>Promoted Semester</th>
                        
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (isset($data['student']) && $data['student']->count() > 0)
                        @php($i=1)
                        @foreach($data['student'] as $student)
                            <tr>
<!--td class="center first-child"><label><input type="checkbox" name="chkIds[]" value="{{ $student->id }}" class="ace" /><span class="lbl"></span></label></td-->
                                <td>{{ $i }}</td>
                                <td><a href="{{ route($base_route.'.view', ['id' => $student->id]) }}">{{ $student->reg_no }}</a>
<span class="scholar_no" style="display:none;">{{ $student->id }}</span>
<span class="old_ssn" style="display:none;">{{ $student->old_ssn }}</span>
                                </td>
<td> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</td>
<td> {{ ViewHelper::getFacultyTitle($student->faculty) }}</td>
<td> {{ ViewHelper::getSemesterTitle($student->semester) }}</td>
<td>{!! Form::select('course[]', $data['faculties'], '',['class'=>'form-control course']) !!}</td>
<td>{!! Form::select('session[]', $ssn_list, '',['class'=>'form-control sessn']) !!}</td>
<td>{!! Form::select('semester[]', $semester_list, '',['class'=>'form-control smstr']) !!}</td>
<td>
    <select name="status[]" class="form-control status">
        <option value="">--Select Status--</option>
        <option>Pass</option>
        <option>Fail</option>
        <option>Passout</option>
    </select>
</td>
                                
                                <!--td>{{ \Carbon\Carbon::parse($student->reg_date)->format('Y-m-d')}}
                                </td-->
                               <td>
                   <a href="" class="btn btn-info btn_promote">Promote</a>
                               </td> 

                            </tr>
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">No {{ $panel }} data found.</td>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
        </div>
        {!! Form::close() !!}
    </div>
</div>


