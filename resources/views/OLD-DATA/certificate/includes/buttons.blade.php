<div class="clearfix hidden-print " >
    <div class="align-right">
        <a class="{!! request()->is('certificate/manage')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.manage') }}"><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;Manage</a>
        <a class="{!! request()->is('certificate/generate')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('certificate.generate') }}"><i class="fa fa-plus" aria-hidden="true"></i> Generate</a>
    </div>
</div>
<hr class="hr-4">