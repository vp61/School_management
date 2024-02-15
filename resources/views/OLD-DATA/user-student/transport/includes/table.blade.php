<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Transport History</h4>
        <div class="clearfix">
            <span class="pull-right tableTools-container"></span>
        </div>
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead >
                <tr>
                    <th>S.N.</th>
                    <th>Route</th>
                    <th>Vehicle</th>
                    <th>Duration (From : To)</th>
                    <th>Total Rent</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>View Details</th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['user']) && $data['user']->count() > 0)
                    @php($i=1)
                    @foreach($data['user'] as $history)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $history->title }} </td>
                            <td>{{ $history->number }}</td>
                            <td>{{ ucwords($history->duration).' ('.Carbon\Carbon::parse($history->from_date)->format('d-m-Y').' To: '.Carbon\Carbon::parse($history->to_date)->format('d-m-Y').')'}}</td>
                            <td>
                              {{$history->total_rent}}
                            </td>
                            @if(!empty($total_paid))
                                @foreach($total_paid as $key => $value)
                                   @foreach($value as $val)
                                     @if($key == $history->id)
                                        <td>{{!empty($val->total_paid)?$val->total_paid:'0'}}</td>
                                        <td>{{$history->total_rent - $val->total_paid}}</td>
                                    @endif
                                   @endforeach
                                @endforeach 
                            @endif
                            <td style="text-align: center;"><a href="transport\view\{{$history->id}}" class="btn btn-primary btn-minier btn-primary">View</a></td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="11">Data not found.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>