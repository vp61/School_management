<div class="clearfix hidden-print " >
    <div class="align-left">
        <a class="{!! request()->is('transport/report')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('transport.report') }}"><i class="fa fa-money" aria-hidden="true"></i>&nbsp;Colection</a>
        <a class="{!! request()->is('transport/report/due')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('transport.report.due') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i>&nbsp;Due Report</a>
    </div>
</div>
<hr class="hr-4">