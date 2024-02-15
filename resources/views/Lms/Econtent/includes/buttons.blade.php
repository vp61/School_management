<div class="clearfix hidden-print " >
    <div class="align-left">
        <a class="{!! request()->is('Lms/Econtent')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('Lms.Econtent') }}"><i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;{{ $panel }} Detail</a>
        <a class="{!! request()->is('Lms/Econtent/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('Lms.Econtent.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;New {{ $panel }}</a>
    </div>
</div>
<hr class="hr-4">