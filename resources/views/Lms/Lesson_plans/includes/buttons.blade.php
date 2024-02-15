<div class="clearfix hidden-print " >
    <div class="align-left">
        <a class="{!! request()->is('Lms/Lesson_plans')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('Lms.Lesson_plans') }}"><i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;Lesson plans Detail</a>
        <a class="{!! request()->is('Lms/Lesson_plans/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('Lms.Lesson_plans.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;New Lesson plans</a>
    </div>
</div>
<hr class="hr-4">