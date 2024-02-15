<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div>
            <table class="table table-striped table-bordered table-hover">
                <tr >
                    <td class="label-info white">Name</td>
                    <td>{{$data['user'][0]->first_name }}</td>
                    <td class="label-info white">Route</td>
                    <td>{{$data['user'][0]->title}} </td>
                    <td class="label-info white">Vehical</td>
                    <td>{{$data['user'][0]->number}} </td>
                </tr>
                <tr >
                    <td class="label-info white">Duration</td>
                    <td>
                        {{ucwords($data['user'][0]->duration)}}
                    </td>
                    <td class="label-info white">From</td>
                    <td>
                       {{Carbon\Carbon::parse($data['user'][0]->from_date)->format('d-M-Y')}}
                    </td>
                    <td class="label-info white">To</td>
                    <td>
                       {{Carbon\Carbon::parse($data['user'][0]->to_date)->format('d-M-Y')}}
                    </td>
                </tr>
               
                <tr >
                    <td class="label-info white">Total Rent</td>
                    <td>{{ $data['user'][0]->total_rent}}</td>
                    <td class="label-info white">Paid</td>
                    <td>@php 
                    	echo (!empty($total_paid[0]->total_paid)?$total_paid[0]->total_paid:'0');
                    @endphp</td>
                    <td class="label-info white">Due</td>
                    <td>@php 
                    	echo (!empty($total_paid[0]->total_paid)?$data['user'][0]->total_rent - $total_paid[0]->total_paid:$data['user'][0]->total_rent);
                    @endphp</td>
                </tr>
                <tr >
                   
                </tr>
            </table>
        </div>
    </div>
</div><!-- /.row -->




