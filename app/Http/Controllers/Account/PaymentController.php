<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use Tzsk\Payu\Facade\Payment;
use App\Http\Controllers\CollegeBaseController;
use App\Models\FeeCollection;
use App\Models\Student;
use Carbon;
use DB;
use Auth;
use Session;

class PaymentController extends CollegeBaseController
{
    protected $base_route = 'account.fees';
    protected $view_path = 'account.fees';
    protected $panel = 'PayuMoney';

     public function payuForm(Request $request)
    {

        $data=[];
        $student = Student::select('students.id','students.reg_no','students.email','students.first_name','students.last_name', 'students.branch_id','ai.mobile_1','br.branch_name','br.Merchant_Key','br.Merchant_Salt')
            ->where('students.id',$request->student_id)
            ->join('addressinfos as ai','ai.students_id','=','students.id')
            ->join('branches as br','br.id','=','students.branch_id')
            ->first();

        if($student) {
            Session::put('Merchant_Key',$student->Merchant_Key);
            Session::put('Merchant_Salt',$student->Merchant_Salt);
            $reg = $student->reg_no;
            $amount = $request->net_balance;
            $fee_masters_id = $request->fee_masters_id;
            //$productInfo = 'REG NO: ' . $reg . ' | : ' . $request->fee_masters_id . ' | PAY FOR: ' . $request->description;
            $productInfo = [
                'STUD_ID'        => $request->student_id,
                'REG_NO'        => $reg,
                'FEE_MASTER_ID' => $request->fee_masters_id,
                'DESCRIPTION'   => $request->description
            ];
            $productInfo = trim($request->description); //json_encode($productInfo);

            $data = [
                'student_id' => $request->student_id,
                'fee_masters_id' => $fee_masters_id,
                'amount' => $amount.".00",
                'email' => $student->email,
                'firstname' => $student->first_name,
                'lastname' => $student->last_name,
                'phone' => $student->mobile_1,
                'productinfo' => $productInfo,
                'fee_masters_id' => $request->fee_masters_id,
                'branch' => $student->branch_name,
                'Merchant_Key' => $student->Merchant_Key,
                'Merchant_Salt' => $student->Merchant_Salt,


            ];
            $data['fees'] =[
                'feehead' =>$request->description,
                'feeamount' => $request->net_balance,




            ];
        }
        // return $data;
        // dd();

        return view(parent::loadDataToView('account.fees.payment.payu.form'), compact('data'));
    }


  public function index(Request $request)
    {
        return view('tzsk::payment_form', [
            'payment' => (new FormBuilder($request))->build()
        ]);
    }




	public function payment(Request $request)
    {
       $branch_id =Session::get('activeBranch'); //Auth::user()->branch_id;
       $org_id = 1; //Auth::user()->org_id;
      
       $attributes = [
    'txnid' => $request['txnid'], # Transaction ID.
    'amount' => $request['amount'], # Amount to be charged.
    'productinfo' =>$request['productinfo'],
    'firstname' => $request['firstname'], # Payee Name.
    'email' => $request['email'], # Payee Email Address.
    'phone' => $request['phone'], # Payee Phone Number.
    'feehead' => $request['udf1'], # Payee Phone Number.
    'branch_id' => $request['branch'], # Payee Phone Number.
    'feeamount' => $request['udf2'], # Payee Phone Number.
];



 $payupayment = DB::table('payu_payments')->insert([
        "account"=>'payu',
        'txnid' =>$request['txnid'], 
        'mihpayid' =>$request['mihpayid'], 
        'firstname' =>$request['firstname'], 
        'email' =>$request['email'],  
        'phone' =>$request['phone'], 
        'amount' =>$request['amount'], 
        'data' =>$request['productinfo'], 
        'status' =>$request['status'], 
        'unmappedstatus' =>$request['unmappedstatus'], 
        'feehead' => $request['udf1'], # Payee Phone Number.
        'feeamount' => $request['udf2'],
        'fee_masters_id' => $request['fee_masters_id'],
        'student_id' => $request['student_id'],
         'branch_id' => $branch_id,
         'org_id' => $org_id,

       
        
]);
 

return Payment::make($attributes, function ($then) {
    $then->redirectTo('payment/status');
    # OR...
    $then->redirectRoute('payment.status');
    # OR...
    $then->redirectAction('Account\PaymentController@status');
});
    }



    public function status()
    {

    $payment = Payment::capture();

    $paySessData = $branch_id =Session::get('tzsk_payu_data.payment'); 


	// Get the payment status.
	$payment->isCaptured();

    // chECK FOR EXISTING RECORDS FOR SAME TXNiD AND mihpayid
    $recInfo = DB::table('payu_payments')
    ->where(function($q) use ($payment){
        if($payment->txnid){
            $q->orWhere('txnid', $payment->txnid)
            ->orWhere('mihpayid', $payment->mihpayid);
        }
    })->get();

    $countStatus = $recInfo->count();
    
    $paymentStatus = ($paySessData['checksumStatus'] == "Tampered")? 'Failed' : $payment->status;
    $temStatus = ($paySessData['checksumStatus'] == "Tampered")? 'Tampered / Failure' : $payment->status;

    //dd($temStatus);
       
    $data = DB::table('payu_payments')->where('txnid', $payment->txnid)
    ->update([
        "account"=>$payment->account,
        'payable_type' =>$payment->payable_type, 
        'txnid' =>$payment->txnid, 
        'firstname' =>$payment->firstname, 
        'email' =>$payment->email, 
        'phone' =>$payment->phone, 
        'amount' =>$payment->amount, 
        'discount' =>$payment->discount, 
        'net_amount_debit' =>$payment->net_amount_debit, 
        'data' =>$payment->data, 
        'status' =>$paymentStatus, 
        'unmappedstatus' =>$payment->unmappedstatus, 
        'mode' =>$payment->mode, 
        'bank_ref_num' =>$payment->bank_ref_num, 
        'mihpayid' =>$payment->mihpayid, 
        'updated_status' =>$temStatus,
        'cardnum' =>$payment->cardnum
    ]);


      $feeheads=DB::table('payu_payments')->select('feehead', 'feeamount','student_id','fee_masters_id','id')->where('txnid' ,$payment->txnid)->Get();
    $user_id = Auth::user()->id;
    $date =  Carbon\Carbon::now();
    $randomString = $this->reciept_no();

     if ($payment->status=='Failed') 
     {

         $paymentdata =DB::table('collect_fee')->insert([
            'created_by'=>$user_id,
            'reciept_no'=>$randomString,
            'created_by'=>$user_id,
            'student_id' =>$feeheads[0]->student_id, 
            'assign_fee_id' =>$feeheads[0]->fee_masters_id, 
            'created_at' => $date, 
             'reciept_date' =>$date,
            'amount_paid' =>$payment->amount,  
            'payment_type' =>$payment->mode,
            'status' =>'0'

        ]);
        
     }
     elseif ($payment->status=='Completed' && $paySessData['checksumStatus'] != "Tampered") 
     { 
            $paymentdata =DB::table('collect_fee')->insert([
        'created_by'=>$user_id,
        'reciept_date' =>$date,
        'reciept_no'=>$randomString,
        'created_by'=>$user_id,
        'student_id' =>$feeheads[0]->student_id, 
        'assign_fee_id' =>$feeheads[0]->fee_masters_id, 
        'created_at' => $date, 
        'amount_paid' =>$payment->amount,  
        'payment_type' =>$payment->mode, 
        'status' =>'1' 
        ]); 
     }
    return view('account.fees.payment.payu.failed', compact('payment', 'feeheads','temStatus'));

    }
    
}
