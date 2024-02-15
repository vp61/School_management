
<div class="clearfix hidden-print " >
    <div class="align-right">
        <a class="{!! request()->is('/timetable/subject/*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('timetable.subject') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Subject</a>
        <a class="{!! request()->is('/timetable/assign/*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('timetable.assign') }}"><i class="fa fa-plus" aria-hidden="true"></i> Assign Subject</a>
    </div>
</div>
<hr class="hr-4">