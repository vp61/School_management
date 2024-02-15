<style type="text/css">
    .blink_me {
  animation: blinker 1s linear infinite;
}

@keyframes blinker {
  
  50% {
    opacity: 0;
  } 
  
}
</style>
<!-- div.table-responsive -->
<div class="table-responsive">
        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Term</th>
                    <th>Type</th>
                    <th>Mode</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['exam']) && $data['exam']->count() > 0)
                    @php($i=1)
                    @foreach($data['exam'] as $exam)
                        <tr class="record">
                            <td>{{ $i }}</td>
                            <td>{{ $exam->title }}</td>
                            <td>{{ $exam->description }}</td>
                            <td>{{ $exam->term }}</td>
                            <td>{{ $exam->type }}</td>
                            <td class="mode" id="{{$exam->mode}}">{{ $exam->mode }}</td>
                            <td>{{ \Carbon\Carbon::parse($exam->date)->format('d-M-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($exam->end_time)->format('h:i A') }}</td>
                            <td>
                                @if($exam->mode_id == 1)
                                    <?php
                                        $time = \Carbon\Carbon::now()->format('H:i:s') ;
                                        $date = \Carbon\Carbon::now()->format('Y-m-d') ;
                                        $parameter = [
                                            'id'=>$exam->id,
                                        ];
                                        $parameter = Crypt::encrypt($parameter);
                                    ?>
                                    @if($date == $exam->date)
                                        @if($time >= $exam->start_time && $time <= $exam->end_time )
                                            <a href="{{ route('user-student.exams.start',['id'=>$parameter])}}" title="Start Exam" class="btn-success btn-sm" >
                                                 Start Eaxm
                                            </a>
                                        @elseif($time < $exam->end_time)
                                           <p class="text-info blink_me"> 
                                                EXAM SCHEDULED AT <b>{{\Carbon\Carbon::parse($exam->start_time)->format('h:i A')}}
                                               </b>
                                           </p>
                                        @else
                                            <span class="btn-warning btn-sm">
                                                EXAM COMPLETED
                                            </span>
                                        @endif
                                    @elseif($date < $exam->date) 
                                        <p class="text-info"> 
                                                EXAM SCHEDULED ON <b>{{ \Carbon\Carbon::parse($exam->date)->format('d-M-Y') }} AT {{\Carbon\Carbon::parse($exam->start_time)->format('h:i A')}}
                                               </b>
                                        </p>
                                    @else    
                                        <span class="btn-warning btn-sm">
                                            EXAM COMPLETED
                                        </span>
                                    @endif
                                @endif    
                            </td>
                            {{--<td class="hidden-480">
                                <a href="{{ route('user-student.exam-schedule', ['year' => $exam->years_id,
                                        'month' => $exam->months_id, 'exam' => $exam->exams_id,'faculty' => $exam->faculty_id,
                                         'semester' => $exam->semesters_id]) }}" title="AdmitCard" class="btn-primary btn-sm" target="_blank">
                                    <i class="fa fa-list-alt" aria-hidden="true"></i> Scedule
                                </a>
                                &nbsp;&nbsp;&nbsp;
                                <a href="{{ route('user-student.exam-admit-card', ['year' => $exam->years_id,
                                        'month' => $exam->months_id, 'exam' => $exam->exams_id,'faculty' => $exam->faculty_id,
                                         'semester' => $exam->semesters_id]) }}" title="AdmitCard" class="btn-primary btn-sm" target="_blank">
                                    <i class="fa fa-user" aria-hidden="true"></i> Admit Card
                                </a>
                                &nbsp;&nbsp;&nbsp;
                                <a href="{{ route('user-student.exam-score', ['year' => $exam->years_id,
                                        'month' => $exam->months_id, 'exam' => $exam->exams_id,'faculty' => $exam->faculty_id,
                                         'semester' => $exam->semesters_id]) }}" title="AdmitCard" class="btn-primary btn-sm" target="_blank">
                                    <i class="fa fa-line-chart" aria-hidden="true"></i> Grade Score
                                </a>
                            </td>--}}
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="8">No {{ $panel }} data found.</td>
                    </tr>
                @endif
            </tbody>
        </table>
</div>
</div>