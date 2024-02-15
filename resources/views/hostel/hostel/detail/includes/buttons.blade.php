<div class="clearfix hidden-print " >
    <div class="align-left">
        <a class="{!! request()->is('hostel/*/block')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('hostel.block',['id'=>$id]) }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Manage Block</a>
         <a class="{!! request()->is('hostel/*/floor')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('hostel.floor', ['id' => $id]) }}"><i class="fa fa-plus" aria-hidden="true"></i> Manage Floor</a>
        <a class="{!! request()->is('hostel/*/room/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('hostel.room.add', ['id' => $id]) }}"><i class="fa fa-plus" aria-hidden="true"></i> Manage Room</a>
        <a class="{!! request()->is('hostel/room-type*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('hostel.bed',['id'=>$id]) }}"><i class="fa fa-plus" aria-hidden="true"></i> Manage Bed</a>
    </div>
</div>
<hr class="hr-4">