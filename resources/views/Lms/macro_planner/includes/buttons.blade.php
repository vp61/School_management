<div class="clearfix hidden-print " >
    <div class="align-left">
        <a class="{!! request()->is('Lms/macro_planner')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('Lms.macro_planner') }}"><i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;Macro plans Detail</a>
        <a class="{!! request()->is('Lms/macro_planner/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('Lms.macro_planner.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;New Macro plans</a>
    </div>
</div>
<hr class="hr-4">