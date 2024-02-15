@ability('super-admin', 'fees-payment-payu-payment')
    <hr class="hr-2">
    <form action="{{route('account.fees.payu-form')}}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="student_id" value="{{ $data['student']->id }}">
        <input type="hidden" name="fee_masters_id" value="{{ $feemaster->id }}">
        <input type="hidden" name="net_balance" value="{{ $net_balance }}">
        <input type="hidden" name="description" value="{{ ViewHelper::getSemesterById($feemaster->semester) }}-{{ ViewHelper::getFeeHeadById($feemaster->fee_head) }}">

        <button type="submit">
            <img alt="PayUMoney Payment Request Form" src="{{ asset('assets/images/paymenticon/payu.jpg') }}" width="70px" height="30px" />
        </button>
    </form>
@endability
