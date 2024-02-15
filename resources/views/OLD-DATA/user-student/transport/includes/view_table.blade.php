<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp; Payment History</h4>
        <div class="clearfix">
            <span class="pull-right tableTools-container"></span>
        </div>
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead >
                <tr>
                    <th>S.N.</th>
                    <th>Receipt No</th>
                    <th>Paid</th>
                    <th>Date</th>
                    <th>Pay Mode</th>
                    <th>Print</th>
                </tr>
                </thead>
                <tbody>
                @if (isset($paid) && $paid->count() > 0)
                    @php($i=1)
                    @foreach($paid as $history)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $history->receipt_no }} </td>
                            <td>{{ $history->amount_paid }}</td>
                            <td>{{ $history->created_at}}</td>
                            <td>{{$history->pay_mode}}</td>
                            <td style="text-align: center;"><a href="\transport\print\{{$history->receipt_no}}" class="btn btn-primary btn-minier btn-primary" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> Print</a></td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="6">Data not found.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>