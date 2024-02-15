<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        <div class="table-header">
            {{ $panel }}  Record list on table. Filter list using search box as your Wish.
        </div>
        <div class="table-responsive">
        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    
                        <th>S.N.</th>
                        <th>Reg. Number</th>
                        <th>Name of Student</th>
                        <th>Father Name</th>
                        <th>Current {{ env('course_label') }}</th>
                        <th>Current Section</th>                       
                        <th></th>
                    
                </thead>
               
                @if (isset($data['student']) && $data['student']->count() > 0)
                    @php($i=1)
                    @foreach($data['student'] as $student)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>
                                <a href="{{ route($base_route.'.view', ['id' => $student->id]) }}">{{ $student->reg_no }}</a>
                                <span class="scholar_no" style="display:none;">{{ $student->id }}</span>
                                <span class="old_ssn" style="display:none;">{{ $student->old_ssn }}</span>
                            </td>
                            <td> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}
                            </td>
                            <td>{{$student->father_name}}</td>
                            <td> {{ ViewHelper::getFacultyTitle($student->faculty) }}</td>
                            <td> {{ ViewHelper::getSemesterTitle($student->semester) }}</td>
                           <td>
                            <a href="{{route('student.transferStudent',[$student->id,$student->requested_session])}}"  class="btn btn-info">Transfer</a>
                           </td> 
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="6">No {{ $panel }} data found.</td>
                    </tr>
                @endif
               
        </table>
    </div>
    </div>
</div>


