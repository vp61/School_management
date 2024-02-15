<div class="clearfix hidden-print " >
    <div class="align-left">
        <a class="{!! request()->is('Lms/micro_planner')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('Lms.micro_planner') }}"><i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;Micro plans Detail</a>
        <a class="{!! request()->is('Lms/micro_planner/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('Lms.micro_planner.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;New Micro plans</a>
    </div>
</div>
<hr class="hr-4">