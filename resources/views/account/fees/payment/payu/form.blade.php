<?php
// Merchant key here as provided by PayUMoney
$payumoney = json_decode($paymentSetting['PayUMoney'],true);
// $MERCHANT_KEY = $payumoney['Merchant_Key'];
//$MERCHANT_KEY = "Xv4hXg0Q";

// Merchant Salt as provided by Payu
//$SALT = $data['Merchant_Salt'];

// End point - change to https://secure.payu.in for if LIVE mode
//$PAYU_BASE_URL = "https://sandboxsecure.payu.in";
$PAYU_BASE_URL = "https://secure.payu.in";

$action = '';

$posted = array();
if(!empty($_POST)) {
//print_r($_POST);
    foreach($_POST as $key => $value) {
        $posted[$key] = $value;

    }
}

$formError = 0;

if(empty($posted['txnid'])) {
// Generate random transaction id
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
    $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
    if(
        empty($posted['key'])
        || empty($posted['txnid'])
        || empty($posted['amount'])
        || empty($posted['firstname'])
        || empty($posted['email'])
        || empty($posted['phone'])
        || empty($posted['productinfo'])
        || empty($posted['surl'])
        || empty($posted['furl'])
        || empty($posted['service_provider'])
    ) {
        $formError = 1;
    } else {
//$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';
        foreach($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }

        $hash_string .= $SALT;


        $hash = strtolower(hash('sha512', $hash_string));
        $action = $PAYU_BASE_URL . '/_payment';
    }
} elseif(!empty($posted['hash'])) {
    $hash = $posted['hash'];
    $action = $PAYU_BASE_URL . '/_payment';
}
?>

@extends('layouts.master')

@section('top-script')
  <!--   <script>
        var hash = '<?php echo $hash ?>';
        function submitPayuForm() {
            if(hash == '') {
                return false;
            }
            var payuForm = document.forms.payuForm;
            payuForm.submit();
        }

        window.onload = submitPayuForm;
    </script> -->


@endSection
@section('css')
    <!-- page specific plugin styles -->

@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        PayU Payment
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Request Form
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('includes.flash_messages')
                    <div class="col-xs-12 ">
                        <div class="easy-link-menu align-right">
                            <img alt="PayUMoney Payment Request Form" src="{{ asset('assets/images/paymenticon/payu.jpg') }}" width="100px" height="70px" />
                        </div>
                        <?php if($formError) { ?>
                        <span style="color:red">Please fill all mandatory fields.</span>
                        <?php } ?>
                        <h4 class="header large lighter blue">Mandatory Parameters</h4>
                        <form action="{{route('payupayment')}}"  name="payuForm" method=GET class="form-horizontal">
                            <div class="form-group">
                                <input type="hidden" name="key" value="{{$data['Merchant_Key'] }}" />
                                <input type="hidden" name="hash" value="{{$data['Merchant_Salt']}}"/>
                                <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Amount:</label>
                                <div class="col-sm-4">
                                    <?php 

                                    $branch_id = Session::get('activeBranch');
                                    $iimtcheck = App\Branch::select('is_iimt')
                                    ->where('id',$branch_id)
                                    ->first(); 
                                    $emailId = (empty($posted['email'])) ? $data['email'] : $posted['email'];
                                    
                                    $amountVal = (empty($posted['amount']))? $data['amount'] : $posted['amount'];
                                    
                                    if($iimtcheck->is_iimt == 1 OR $amountVal > 1000) { ?>
                                        <input type="number" name="amount" value="<?php echo (empty($posted['amount'])) ? $data['amount'] : $posted['amount'] ?>" class="input-sm form-control border-form" min="1000" required/>
                                    <?php }else{ ?>
                                        <input type="hidden" name="amount" value="<?php echo (empty($posted['amount'])) ? $data['amount'] : $posted['amount'] ?>" class="input-sm form-control border-form" required/>
                                        <?php echo (empty($posted['amount'])) ? $data['amount'] : $posted['amount'] ?>
                                    <?php } ?>
                                </div>

                                 <label class="col-sm-2 control-label">Branch:</label>
                                <div class="col-sm-4">
                                    <input name="branch" value="{{$data['branch'] }}" class="input-sm form-control border-form" readonly/>
                                </div>

                                
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Name:</label>
                                <div class="col-sm-4">
                                    <input name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? $data['firstname'] : $posted['firstname']; ?>"   class="input-sm form-control border-form" required />
                                </div>
                                <label class="col-sm-2 control-label">Pay For :</label>
                                <div class="col-sm-4">
                                    <textarea name="productinfo" required class="input-sm form-control border-form" readonly><?php echo (empty($posted['productinfo'])) ? $data['productinfo'] : $posted['productinfo'] ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Phone:</label>
                                <div class="col-sm-4">
                                    <input name="phone" value="<?php echo (empty($posted['phone'])) ? $data['phone'] : $posted['phone']; ?>"  class="input-sm form-control border-form" required />
                                </div>
                                <label class="col-sm-2 control-label">Email:</label>
                                <div class="col-sm-4">
                                    <input name="email" id="email" value="<?php echo (empty($posted['email'])) ? $data['email'] : $posted['email']; ?>"  class="input-sm form-control border-form" required />
                                </div>

                                
                            </div>

                            <h4 class="header large lighter blue">&nbsp;Optional Parameters</h4>

                            <div class="form-group hidden">
                                <label class="col-sm-2 control-label">Cancel URI:</label>
                                <div class="col-sm-4">
                                    <input name="curl"  value=""   class="input-sm form-control border-form" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Address1:</label>
                                <div class="col-sm-4">
                                    <input name="address1" class="input-sm form-control border-form" />
                                </div>

                                <label class="col-sm-2 control-label">Address2:</label>
                                <div class="col-sm-4">
                                    <input name="address2" class="input-sm form-control border-form" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">City:</label>
                                <div class="col-sm-4">
                                    <input name="city" class="input-sm form-control border-form" />
                                </div>

                                <label class="col-sm-2 control-label">State:</label>
                                <div class="col-sm-4">
                                    <input name="state" class="input-sm form-control border-form" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Country:</label>
                                <div class="col-sm-4">
                                    <input name="country" class="input-sm form-control border-form" />
                                </div>

                                <label class="col-sm-2 control-label">Zipcode:</label>
                                <div class="col-sm-4">
                                    <input name="zipcode" class="input-sm form-control border-form" />
                                </div>
                            </div>

                            <div class="form-group hidden">
                                <label class="col-sm-2 control-label">UDF1:</label>
                                <div class="col-sm-4">
                                    <input name="udf1" value= "{{$data['fees']['feehead']}}" class="input-sm form-control border-form" />
                                </div>

                                <label class="col-sm-2 control-label">UDF2:</label>
                                <div class="col-sm-4">
                                    <input name="udf2"  value= "{{$data['fees']['feeamount']}}" class="input-sm form-control border-form" />
                                </div>
                            </div>
                             <div class="form-group hidden">
                                <label class="col-sm-2 control-label">status:</label>
                                <div class="col-sm-4">
                                    <input name="status" value= "pending" class="input-sm form-control border-form" />
                                </div>

                                <label class="col-sm-2 control-label">unmappedstatus:</label>
                                <div class="col-sm-4">
                                    <input name="unmappedstatus"  value= "pending" class="input-sm form-control border-form" />
                                </div>
                                 <label class="col-sm-2 control-label">Account:</label>
                                <div class="col-sm-4">
                                    <input name="account"  value= "payu" class="input-sm form-control border-form" />
                                </div>
                                 <label class="col-sm-2 control-label">mihpayid:</label>
                                <div class="col-sm-4">
                                    <input name="mihpayid"  value= "pay#$u98684334" class="input-sm form-control border-form" />
                                </div>
                                <label class="col-sm-2 control-label">student_id:</label>
                                <div class="col-sm-4">
                                    <input name="student_id"  value= "{{$data['student_id']}}" class="input-sm form-control border-form" />
                                </div>
                                <label class="col-sm-2 control-label">fee_masters_id:</label>
                                <div class="col-sm-4">
                                    <input name="fee_masters_id"  value= "{{$data['fee_masters_id']}}" class="input-sm form-control border-form" />
                                </div>
                            </div>


                            <div class="form-group hidden">
                                <label class="col-sm-2 control-label">UDF3:</label>
                                <div class="col-sm-4">
                                    <input name="udf3" class="input-sm form-control border-form" />
                                </div>

                                <label class="col-sm-2 control-label">UDF4:</label>
                                <div class="col-sm-4">
                                    <input name="udf4" class="input-sm form-control border-form" />
                                </div>
                            </div>

                            <div class="form-group hidden">
                                <label class="col-sm-2 control-label">UDF5:</label>
                                <div class="col-sm-4">
                                    <input name="udf5" class="input-sm form-control border-form" />
                                </div>

                                <label class="col-sm-2 control-label">PG:</label>
                                <div class="col-sm-4">
                                    <input name="pg" class="input-sm form-control border-form" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="clearfix form-actions">
                                    <div class="align-right">
                                        <?php if(!$hash) { ?>
                                        <button class="btn btn-info"   type="submit" value="Submit">
                                            Pay Now With PayU
                                        </button>
                                        <?php } ?>&nbsp; &nbsp; &nbsp;
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
        </div><!-- /.page-content -->
    </div>
    </div><!-- /.main-content -->
@endsection


@section('js')

@endsection