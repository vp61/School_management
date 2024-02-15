<div class="form-horizontal">
    <div class="row">
        <div class="col-xs-12">
            <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        
            <hr class="hr-12">
            <div class="table-header">
                {{ $panel }}  Record list on table. Filter list using search box as your Wish.
            </div>
            <!-- div.table-responsive -->
            <div class="table-responsive">
                {{--{!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}--}}
                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                    <thead >
                        <tr>
                            <th class="center">
                                <label class="pos-rel">
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </th>
                            <th>S.N.</th>
                            <th>Routes</th>
                            <th>Vehicle</th>
                            <th>Type</th>
                            <th>Reg. No. </th>
                            <th>Name </th>
                            <th>Rent</th>
                            <th>Paid</th>
                            <th>Receipt No.</th>
                            <th>Date</th>
                            <th>Mode</th>
                            <th>Ref No</th>
                            <th>Print</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if (isset($data['user']) && $data['user']->count() > 0)
                        @php($i=1)
                        @foreach($data['user'] as $user)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ $user->id }}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $i }}</td>
                                <td>{{ $user->routes_id==""?"":ViewHelper::getRouteNameById($user->routes_id) }} </td>
                                <td>{{ $user->vehicles_id ==""?"":ViewHelper::getVehicleById($user->vehicles_id) }}</td>
                                <td>{{ $user->member_type==1?"Student":"Staff" }}</td>
                                <td>
                                    @if($user->member_type == 1)
                                        <a href="{{ route('student.view', ['id' => $user->member_id]) }}">
                                            {{ ViewHelper::getStudentById($user->member_id) }}
                                        </a>
                                    @else
                                        <a href="{{ route('staff.view', ['id' => $user->member_id]) }}">
                                            {{ ViewHelper::getStaffById($user->member_id) }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($user->member_type==1)
                                        {{ ViewHelper::getStudentNameById($user->member_id) }}
                                    @else
                                        {{ ViewHelper::getStaffNameById($user->member_id) }}
                                    @endif
                                </td>
                                <td>
                                    {{$user->total_rent}}
                                </td>
                                <td>
                                  <b> {{$user->amount_paid}} </b>
                                </td>
                                <td>
                                    {{$user->receipt_no}}
                                </td>
                                <td>
                                    {{date('d-m-Y', strtotime($user->created_at))}}
                                </td>
                                <td>
                                    {{$user->pay_mode}}
                                </td> 
                                <td>
                                    {{$user->ref_no}}
                                </td> 
                                <td style="text-align: center;">
                                    <a href="print/{{$user->receipt_no}}" target="_blank"><i class="fa fa-print fa-2x" aria-hidden="true"></i></a>
                                </td>       
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="14">No {{ $panel }} data found.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        {!! Form::close() !!}
        </div>
    </div>
</div>

