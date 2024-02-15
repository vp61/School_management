<div class="row">
    <div class="col-xs-12">
    @include('includes.data_table_header')
        <!-- div.table-responsive -->
        {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>S.N.</th>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Head</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <!-- <th>Dr Amount</th>
                        <th>Cr Amount</th> -->
                        <th>Description</th>
                        <th>Note</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (isset($data['transaction']) && $data['transaction']->count() > 0)
                        @php($i=1)
                        @foreach($data['transaction'] as $transaction)
                     
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ $transaction->id }}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>

                                </td>
                                <td>{{ $i }}</td>
                                <td> {{ $transaction->branch_name }}</td>
                                <td nowrap>{{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d')}}
                                </td>
                                <td> {{ ViewHelper::getTransactionHeadById($transaction->tr_head_id) }}</td>
                                <td> {{ $transaction->type }}</td>
                                <td> {{ $transaction->amount }}</td>
                                <td> {{ $transaction->description }}</td>
                                <td>{{ $transaction->note }}</td>
                                <td>
                                    <div class="hidden-sm hidden-xs action-buttons">
                                        <a class="green" href="{{ route($base_route.'.edit', ['id' => $transaction->id]) }}">
                                            <i class="ace-icon fa fa-pencil bigger-130"></i>
                                        </a>
<a href="{{ route($base_route.'.printed', ['id' => $transaction->id]) }}" class="tooltip-error" data-rel="tooltip" title="Print">
    <span><i class="ace-icon fa fa-print bigger-120"></i></span>
</a>                                
                                @ability('super-admin','super-admin')
                                        <a href="{{ route($base_route.'.delete', ['id' => $transaction->id]) }}" class="red bootbox-confirm">
                                            <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                        </a>
                                @endability        
                                    </div>
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach
                        {{--<tr class="blue" style="font-size: 16px; font-weight: 600;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="align-right" colspan="5">Total </td>
                            <td>{{ $income = $data['transaction']->sum('dr_amount') }} </td>
                            <td>{{ $expenses = $data['transaction']->sum('cr_amount') }} </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="green" style="font-size: 16px; font-weight: 600;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td  class="align-right">Profit & Loss </td>
                            <td >{{ $income - $expenses }} </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>--}}

                    @else
                        <tr>
                            <td colspan="10">No {{ $panel }} data found.</td>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
        </div>
        {!! Form::close() !!}
    </div>
</div>


