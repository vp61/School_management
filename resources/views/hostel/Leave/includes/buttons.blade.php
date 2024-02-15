<div class="clearfix hidden-print " >
    <div class="align-left">
        <a class="{!! request()->is('hostel')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('hostel') }}"><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;Detail</a>
        <a class="{!! request()->is('hostel/Leave/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('hostel.Leave.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>Apply Leave</a>
       
    </div>
</div>
<hr class="hr-4">