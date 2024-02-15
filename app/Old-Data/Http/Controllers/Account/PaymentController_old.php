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


class PaymentController extends CollegeBaseController
{
    protected $base_route = 'account.fees';
    protected $view_path = 'account.fees';
    protected $panel = 'PayuMoney';

     public function payuForm(Request $request)
    {

        $data=[];
        $student = Student::select('students.id','students.reg_no','students.email','students.first_name','students.last_name','ai.mobile_1')
            ->where('students.id',$request->student_id)
            ->join('addressinfos as ai','ai.students_id','=','students.id')
            ->first();

        if($student) {
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
            $productInfo = json_encode($productInfo);

            $data = [
                'student_id' => $request->student_id,
                'fee_masters_id' => $fee_masters_id,
                'amount' => $amount,
                'email' => $student->email,
                'firstname' => $student->first_name,
                'lastname' => $student->last_name,
                'phone' => $student->mobile_1,
                'productinfo' => $productInfo,
                'fee_masters_id' => $request->fee_masters_id,

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
       
      
       $attributes = [
    'txnid' => $request['txnid'], # Transaction ID.
    'amount' => $request['amount'], # Amount to be charged.
    'productinfo' =>$request['productinfo'],
    'firstname' => $request['firstname'], # Payee Name.
    'email' => $request['email'], # Payee Email Address.
    'phone' => $request['phone'], # Payee Phone Number.
    'feehead' => $request['udf1'], # Payee Phone Number.
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

	// Get the payment status.
	 $payment->isCaptured();

   
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
        'status' =>$payment->status, 
        'unmappedstatus' =>$payment->unmappedstatus, 
        'mode' =>$payment->mode, 
        'bank_ref_num' =>$payment->bank_ref_num, 
        'mihpayid' =>$payment->mihpayid,
        'cardnum' =>$payment->cardnum
        ]);


      $feeheads=DB::table('payu_payments')->select('feehead', 'feeamount','student_id','fee_masters_id','id')->where('txnid' ,$payment->txnid)->Get();

        
     

     if ($payment->status=='Failed') {
        return view('account.fees.payment.payu.failed', compact('payment', 'feeheads'));
     }
      if ($payment->status=='Completed') {
        $user_id = Auth::user()->id;
        $date =  Carbon\Carbon::now();

     $paymentdata =DB::table('fee_collections')->insert([
        'created_by'=>$user_id,
        'students_id' =>$feeheads[0]->student_id, 
        'fee_masters_id' =>$feeheads[0]->fee_masters_id, 
        'date' => $date, 
        'paid_amount' =>$payment->amount,  
        'payment_mode' =>$payment->mode,
        'onlinepayment_id' =>$feeheads[0]->id,
        'status' =>'1'

        ]);
 
       return view('account.fees.payment.payu.failed', compact('payment', 'feeheads'));
     }
 //        return response()->json($payment);
 //    dd();


     
	// # Returns boolean - true / false
	// return redirect()->route('home');
    
    }
    
}
