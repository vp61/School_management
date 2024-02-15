<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|

| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*PayU routes*/

Route::get('/home',function(){
  return redirect('/');
});
# Call Route
Route::get('payment', ['as' => 'payupayment', 'uses' => 'Account\PaymentController@payment']);

# Status Route
Route::get('payment/status', ['as' => 'payment.status', 'uses' => 'Account\PaymentController@status']);
//No Due Route
Route::get('', ['as' => 'payment.status', 'uses' => 'Account\PaymentController@status']);

/*Auth Routes*/
Auth::routes();
//logout route
Route::get('logout', 'Auth\LoginController@showLoginForm');
 //enquiry routes
Route::get('enquiry',['as' => '.enquiry',    'middleware' => 'auth'    , 'uses' => 'EnquiryController@index']);
Route::post('enquiry/add_followup',['as' => '.enquiry.add_followup',    'middleware' => 'auth'    , 'uses' => 'EnquiryController@add_followup']);
Route::get('enquiry/followup/{id}/delete',['as' => '.enquiry.followup.delete',    'middleware' => 'auth'    , 'uses' => 'EnquiryController@delete_followup']);

Route::post('enquiry/loadenquiry','EnquiryController@loadenquiry')->name('enquiry.loadenquiry');

Route::post('enquiry',['as' => '.enquiry',   'middleware' => 'auth'    ,          'uses' => 'EnquiryController@store']);
Route::get('enquirylist',['as' => '.enquiry_list',    'middleware' =>['ability:super-admin|account,enquiry-list','auth']    ,  'uses' => 'EnquiryController@enquirylist']);
Route::get('enquirystatus',['as' => '.enquiry_status',    'middleware' =>['ability:super-admin|account,enquiry-report','auth']    ,  'uses' => 'EnquiryController@enquirystatus']);
Route::post('enquirystatus',['as' => '.enquiry_status',    'middleware' =>['ability:super-admin|account,enquiry-report','auth']    ,  'uses' => 'EnquiryController@enquirystatus']);
Route::get('enquiry/edit/{id}',['as' => '.enquiryedit',            'uses' => 'EnquiryController@enquiryedit']);
Route::post('enquiry/update/{id}',['as' => '.enquiryupdate',            'uses' => 'EnquiryController@enquiryupdate']);
//admission route
Route::get('admission',['as' => '.admission',    'middleware' =>['ability:super-admin|account,prospectus-sale','auth']    ,     'uses' => 'AdmissionController@index']);
Route::get('admission_print/{id}',['as' => '.admission_print', 'middleware' => 'auth' , 'uses' => 'AdmissionController@admission_print']);
Route::get('admission/{id}',['as' => '.admission_form',    'middleware' => 'auth' ,         'uses' => 'AdmissionController@enquiry']);
Route::post('admission',['as' => '.admission',      'middleware' =>['ability:super-admin|account,prospectus-sale','auth'] ,       'uses' => 'AdmissionController@store']);
Route::get('admissionlist',['as' => '.admission_list',      'middleware' =>['ability:super-admin|account,prospectus-list','auth'] ,       'uses' => 'AdmissionController@admissionlist']);
Route::get('admission/edit/{id}',['as' => '.admissionedit',  'middleware' => 'auth' ,           'uses' => 'AdmissionController@admissionedit']);
Route::post('admission/update/{id}',['as' => '.admissionupdate',   'middleware' => 'auth' ,          'uses' => 'AdmissionController@admissionupdate']);
Route::get('switchbranch/{id}',['as' => '.switchbranch',     'middleware' => 'auth' ,        'uses' => 'HomeController@switchbranch']);
Route::post('pdf' , 'EventsController@PDFGenerator');
//student payment
 Route::post('fees/payu-form',                 ['as' => 'fees.payu-form',        'middleware' => 'auth' ,          'uses' => 'Account\PaymentController@payuForm']);

    /*for Dashboard's*/
Route::get('/',                             ['as' => 'home',   'middleware' => 'auth' ,  'uses' => 'HomeController@index']);

/* ONLINE ADMISSION ROUTES*/
Route::group(['prefix'=>'OnlineAdmission/',  'as'=>'OnlineAdmission', 'namespace'=>'OnlineAdmission\\'], function(){
  Route::get('/','OnlineAdmissionController@index');
  Route::post('/','OnlineAdmissionController@store')->name('OnlineAdmission');
  Route::get('/ConfirmPayuPayment',['as' => '.ConfirmPayuPayment', 'uses' => 'OnlineAdmissionController@OnlinePayuPayment']);
  
  Route::get('/ConfirmEasyPayPayment',['as' => '.ConfirmEasyPayPayment', 'uses' => 'OnlineAdmissionController@OnlineEasyPayPayment']);
  Route::get('/PayuStatus',['as' => '.PayuStatus', 'uses' => 'OnlineAdmissionController@PayuStatus']);
  Route::post('/loadCourse','OnlineAdmissionController@loadCourse')->name('.loadCourse');
  Route::post('/getBranchBySeaType','OnlineAdmissionController@getBranchBySeaType')->name('.getBranchBySeaType');
  Route::get('/Payment-Status/{txnid}',['as' => '.Payment-Status', 'uses' => 'OnlineAdmissionController@status']);
  Route::get('/icici',['as' => '.icici', 'uses' => 'OnlineAdmissionController@iciciTest']);
  Route::post('/icici',['as' => '.icici', 'uses' => 'OnlineAdmissionController@iciciTest']);
  
});
/*Inventory*/
Route::group(['prefix'=>'Inventory/','as'=>'inventory','namespace'=>'Inventory\\'],function(){
  /*BRAND*/
  Route::get('brand', ['as'=>'.brand','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'BrandController@index']);
  Route::post('brand/store', ['as'=>'.brand.store','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'BrandController@store']);
  Route::get('brand/edit/{id}', ['as'=>'.brand.edit','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'BrandController@edit']);
  Route::post('brand/edit/{id}', ['as'=>'.brand.edit','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'BrandController@edit']);
  Route::get('brand/delete/{id}', ['as'=>'.brand.delete','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'BrandController@delete']);
  /*Unit*/
  Route::get('units', ['as'=>'.units','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'UnitsController@index']);
  Route::post('units/store', ['as'=>'.units.store','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'UnitsController@store']);
  Route::get('units/edit/{id}', ['as'=>'.units.edit','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'UnitsController@edit']);
  Route::post('units/edit/{id}', ['as'=>'.units.edit','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'UnitsController@edit']);
  Route::get('units/delete/{id}', ['as'=>'.units.delete','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'UnitsController@delete']);

  /*Category*/
  Route::get('category', ['as'=>'.category','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'CategoryController@index']);
  Route::post('category/store', ['as'=>'.category.store','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'CategoryController@store']);
  Route::get('category/edit/{id}', ['as'=>'.category.edit','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'CategoryController@edit']);
  Route::post('category/edit/{id}', ['as'=>'.category.edit','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'CategoryController@edit']);
  Route::get('category/delete/{id}', ['as'=>'.category.delete','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'CategoryController@delete']);
  /*Label*/
  Route::get('label', ['as'=>'.label','middleware'=>['ability:super-admin|account,  inventory-setup','auth'],'uses'=>'LabelController@index']);

  Route::post('label/store', ['as'=>'.label.store','middleware'=>['ability:super-admin|account,  inventory-setup','auth'],'uses'=>'LabelController@store']);
  Route::get('label/edit/{id}', ['as'=>'.label.edit','middleware'=>['ability:super-admin|account, inventory-setup','auth'],'uses'=>'LabelController@edit']);
  Route::post('label/edit/{id}', ['as'=>'.label.edit','middleware'=>['ability:super-admin|account,  inventory-setup','auth'],'uses'=>'LabelController@edit']);
  Route::get('label/delete/{id}',['as'=>'.label.delete','middleware'=>['ability:super-admin|account,  inventory-setup','auth'],'uses'=>'LabelController@delete']);
  /*Product*/
  Route::get('product', ['as'=>'.product','middleware'=>['ability:super-admin|account,product-index','auth'],'uses'=>'ProductController@index']);
   Route::get('product/add', ['as'=>'.product.add','middleware'=>['ability:super-admin|account','auth'],'uses'=>'ProductController@add']);
  Route::post('product/store', ['as'=>'.product.store','middleware'=>['ability:super-admin|account','auth'],'uses'=>'ProductController@store']);
  Route::get('product/edit/{id}', ['as'=>'.product.edit','middleware'=>['ability:super-admin|account','auth'],'uses'=>'ProductController@edit']);
  Route::post('product/edit/{id}', ['as'=>'.product.edit','middleware'=>['ability:super-admin|account','auth'],'uses'=>'ProductController@edit']);
  /*REMOVE IMAGE AJAX*/
  Route::post('product/remove_image', ['as'=>'.product.remove_image','middleware'=>['ability:super-admin|account','auth'],'uses'=>'ProductController@remove_image']);
  /*END AJAX*/
  Route::get('product/delete/{id}', ['as'=>'.product.delete','middleware'=>['ability:super-admin|account','auth'],'uses'=>'ProductController@delete']);
  Route::post('load_subcategory', ['as'=>'.load_subcategory','middleware'=>['ability:super-admin|account','auth'],'uses'=>'ProductController@load_subcategory']);
  /*GST*/
  Route::get('gst', ['as'=>'.gst','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'GstController@index']);
  Route::post('gst/store', ['as'=>'.gst.store','middleware'=>'auth',['ability:super-admin|account,inventory-setup'],'uses'=>'GstController@store']);
  Route::get('gst/edit/{id}', ['as'=>'.gst.edit','middleware'=>'auth',['ability:super-admin|account,inventory-setup'],'uses'=>'GstController@edit']);
  Route::post('gst/edit/{id}', ['as'=>'.gst.edit','middleware'=>'auth',['ability:super-admin|account,inventory-setup'],'uses'=>'GstController@edit']);
  Route::get('gst/delete/{id}', ['as'=>'.gst.delete','middleware'=>'auth',['ability:super-admin|account,inventory-setup'],'uses'=>'GstController@delete']);
  /*SUPPLIER*/
  Route::get('supplier', ['as'=>'.supplier','middleware'=>['ability:super-admin|account,supplier-index','auth'],'uses'=>'SupplierController@index']);

  Route::post('supplier/store', ['as'=>'.supplier.store','middleware'=>'auth',['ability:super-admin|account','auth'],'uses'=>'SupplierController@store']);
  Route::get('supplier/edit/{id}', ['as'=>'.supplier.edit','middleware'=>'auth',['ability:super-admin|account','auth'],'uses'=>'SupplierController@edit']);
  Route::post('supplier/edit/{id}', ['as'=>'.supplier.edit','middleware'=>'auth',['ability:super-admin|account','auth'],'uses'=>'SupplierController@edit']);
  Route::get('supplier/delete/{id}', ['as'=>'.supplier.delete','middleware'=>'auth',['ability:super-admin|account','auth'],'uses'=>'SupplierController@delete']);
  /*Purchase*/
  Route::get('purchase', ['as'=>'.purchase','middleware'=>['ability:super-admin|account,purchase-index','auth'],'uses'=>'PurchaseController@index']);
  Route::post('purchase/store', ['as'=>'.purchase.store','middleware'=>['ability:super-admin|account','auth'],'uses'=>'PurchaseController@store']);
  Route::get('purchase/edit/{id}', ['as'=>'.purchase.edit','middleware'=>['ability:super-admin|account','auth'],'uses'=>'PurchaseController@edit']);
  Route::post('purchase/edit/{id}', ['as'=>'.purchase.edit','middleware'=>['ability:super-admin|account','auth'],'uses'=>'PurchaseController@edit']);
  Route::get('purchase/delete/{id}', ['as'=>'.purchase.delete','middleware'=>['ability:super-admin|account','auth'],'uses'=>'PurchaseController@delete']);
  /*Purchase*/
  Route::get('PurchaseStatus', ['as'=>'.purchaseStatus','middleware'=>['ability:super-admin|account,inventory-setup','auth'],'uses'=>'PurchaseStatusController@index']);
  Route::post('PurchaseStatus/store', ['as'=>'.purchaseStatus.store','middleware'=>'auth',['ability:super-admin|account,inventory-setup'],'uses'=>'PurchaseStatusController@store']);
  Route::get('PurchaseStatus/edit/{id}', ['as'=>'.purchaseStatus.edit','middleware'=>'auth',['ability:super-admin|account,inventory-setup'],'uses'=>'PurchaseStatusController@edit']);
  Route::post('PurchaseStatus/edit/{id}', ['as'=>'.purchaseStatus.edit','middleware'=>'auth',['ability:super-admin|account,inventory-setup'],'uses'=>'PurchaseStatusController@edit']);
  Route::get('PurchaseStatus/delete/{id}', ['as'=>'.purchaseStatus.delete','middleware'=>'auth',['ability:super-admin|account,inventory-setup'],'uses'=>'PurchaseStatusController@delete']);
});

/* Roles Routes */
Route::get('role',                    ['as' => 'role',                  'middleware' =>'auth', ['ability:super-admin|account,role-index'],         'uses' => 'RoleController@index']);
Route::get('role/add',                ['as' => 'role.add',              'middleware' => 'auth',['ability:super-admin,role-add'],           'uses' => 'RoleController@create']);
Route::post('role/store',             ['as' => 'role.store',            'middleware' => 'auth',['ability:super-admin,role-add'],           'uses' => 'RoleController@store']);
Route::get('role/{id}/edit',          ['as' => 'role.edit',             'middleware' => 'auth',['ability:super-admin,role-edit'],          'uses' => 'RoleController@edit']);
Route::post('role/{id}/update',       ['as' => 'role.update',           'middleware' => 'auth',['ability:super-admin,role-edit'],          'uses' => 'RoleController@update']);
Route::get('role/{id}/view',          ['as' => 'role.view',             'middleware' =>'auth', ['ability:super-admin,role-view'],          'uses' => 'RoleController@show']);
Route::get('role/{id}/delete',        ['as' => 'role.delete',           'middleware' => 'auth',['ability:super-admin,role-delete'],        'uses' => 'RoleController@destroy']);

/* User Routes */
Route::get('user',                    ['as' => 'user',                  'middleware' =>'auth', ['ability:super-admin,user-index'],             'uses' => 'UserController@index']);
Route::get('user/add',                ['as' => 'user.add',              'middleware' => 'auth',['ability:super-admin,user-add'],               'uses' => 'UserController@add']);
Route::post('user/store',             ['as' => 'user.store',            'middleware' => 'auth',['ability:super-admin,user-add'],               'uses' => 'UserController@store']);
Route::get('user/{id}/edit',          ['as' => 'user.edit',             'middleware' => 'auth',['ability:super-admin,profile-edit'],              'uses' => 'UserController@edit']);
Route::post('user/{id}/update',       ['as' => 'user.update',           'middleware' => 'auth',['ability:super-admin,user-edit'],              'uses' => 'UserController@update']);
Route::get('user/{id}/delete',        ['as' => 'user.delete',           'middleware' =>'auth', ['ability:super-admin,user-delete'],            'uses' => 'UserController@delete']);
Route::get('user/{id}/active',        ['as' => 'user.active',           'middleware' => 'auth',['ability:super-admin,user-active'],            'uses' => 'UserController@Active']);
Route::get('user/{id}/in-active',     ['as' => 'user.in-active',        'middleware' => 'auth',['ability:super-admin,user-in-active'],         'uses' => 'UserController@inActive']);
Route::post('user/bulk-action',       ['as' => 'user.bulk-action',      'middleware' => 'auth',['ability:super-admin,user-bulk-action'],       'uses' => 'UserController@bulkAction']);

/*user-student route group*/
    Route::group(['prefix' => 'user-student/',          'as' => 'user-student',       'namespace' => 'UserStudent\\'], function () {
    Route::get('',                      ['as' => '',    'middleware' =>'auth',                'uses' => 'HomeController@index']);

    Route::get('profile',               ['as' => '.profile',    'middleware' =>'auth',           'uses' => 'HomeController@profile']);
    Route::post('{id}/password',        ['as' => '.password',    'middleware' =>'auth',          'uses' => 'HomeController@password']);
    Route::get('fees',                  ['as' => '.fees',         'middleware' =>'auth',         'uses' => 'HomeController@fees']);
    Route::get('library',               ['as' => '.library',       'middleware' =>'auth',        'uses' => 'HomeController@library']);
    Route::get('attendance',            ['as' => '.attendance',    'middleware' =>'auth',        'uses' => 'HomeController@attendance']);
    Route::get('exams', ['as' => '.exams',      'middleware' =>'auth',          'uses' => 'HomeController@exams']);
    Route::get('exams/start/{id}', ['as' => '.exams.start',      'middleware' =>'auth',          'uses' => 'HomeController@start_exam']);
    Route::post('exams/submit', ['as' => '.exams.submit',      'middleware' =>'auth',          'uses' => 'HomeController@submit_exam']);

    Route::get('exam-schedule/{year}/{month}/{exam}/{faculty}/{semester}',         ['as' => '.exam-schedule',   'middleware' =>'auth',     'uses' => 'HomeController@examSchedule']);
    Route::get('exam-admit-card/{year}/{month}/{exam}/{faculty}/{semester}',       ['as' => '.exam-admit-card',    'middleware' =>'auth',  'uses' => 'HomeController@admitCard']);
    Route::get('exam-score/{year}/{month}/{exam}/{faculty}/{semester}',            ['as' => '.exam-score',    'middleware' =>'auth',       'uses' => 'HomeController@examScore']);

    Route::get('hostel',                ['as' => '.hostel',     'middleware' =>'auth',         'uses' => 'HomeController@hostel']);
    Route::get('transport',             ['as' => '.transport',   'middleware' =>'auth',        'uses' => 'HomeController@transport']);
     Route::get('transport/view/{id}',             ['as' => '.transport.view',   'middleware' =>'auth',        'uses' => 'HomeController@transportDetail']);
    Route::get('subject',               ['as' => '.subject',     'middleware' =>'auth',        'uses' => 'HomeController@subject']);
    Route::get('notice',                ['as' => '.notice',     'middleware' =>'auth',         'uses' => 'HomeController@notice']);
    
    Route::get('download',              ['as' => '.download',   'middleware' =>'auth',           'uses' => 'HomeController@download']);

    Route::get('assignment',                                    ['as' => '.assignment',  'middleware' =>'auth',      'uses' => 'HomeController@assignment']);
    Route::get('assignment/answer/{id}/add',                    ['as' => '.assignment.answer.add',    'middleware' =>'auth',            'uses' => 'HomeController@addAnswer']);
    Route::post('assignment/answer/store',                      ['as' => '.assignment.answer.store',    'middleware' =>'auth',          'uses' => 'HomeController@storeAnswer']);
    Route::get('assignment/answer/{id}/edit',                   ['as' => '.assignment.answer.edit',       'middleware' =>'auth',        'uses' => 'HomeController@editAnswer']);
    Route::post('assignment/answer/{id}/update',                ['as' => '.assignment.answer.update',    'middleware' =>'auth',         'uses' => 'HomeController@updateAnswer']);
    Route::get('assignment/answer/{id}/{answer}/view',         ['as' => '.assignment.answer.view',       'middleware' =>'auth',        'uses' => 'HomeController@viewAssignmentAnswer']);
     Route::post('assignment/answer/{id}/{answer}/view',         ['as' => '.assignment.answer.view',       'middleware' =>'auth',        'uses' => 'HomeController@viewAssignmentAnswer']);
     Route::get('timetable',         ['as' => '.timetable',       'middleware' =>'auth',        'uses' => 'HomeController@viewTimetable']);
     Route::get('live_class',         ['as' => '.live_class',       'middleware' =>'auth',        'uses' => 'HomeController@live_class']);
     Route::get('meeting',         ['as' => '.meeting',       'middleware' =>'auth',        'uses' => 'HomeController@meetings']);
     Route::get('meeting/join/{meeting_id}',         ['as' => '.meeting.join',       'middleware' =>'auth',        'uses' => 'HomeController@join_meeting']);
      Route::get('live_class/join_class/{meeting_id}/{meeting_name}',         ['as' => '.live_class.join_class',       'middleware' =>'auth',        'uses' => 'HomeController@join_class']);
    
    //enquiry routes
   Route::get('enquiry',               ['as' => '.enquiry',   'middleware' =>['ability:super-admin|account,enquiry-form','auth'],          'uses' => 'EnquiryController@index']);
});
/*Certificate Route Group */
Route::group(['prefix'=>'certificate/',                'as'=>'certificate',          'namespace'=>'Certificate\\'],function(){

    Route::get('manage',               ['as' => '.manage',                'middleware' =>['ability:super-admin|account,manage-certificate-index','auth'],             'uses' => 'ManageCertificateController@index']);
    Route::post('store',               ['as' => '.store',                'middleware' =>['ability:super-admin|account,manage-certificate-index','auth'],             'uses' => 'ManageCertificateController@store']);

    Route::get('generate',['as'=>'.generate','middleware'=>['ability:super-admin|account,generate-certificate','auth'],'uses'=>'CertificateController@index']);

    Route::post('generate',['as'=>'.generate','middleware'=>['ability:super-admin|account,generate-certificate','auth'],         'uses'=>'CertificateController@generate_certificate']);

    Route::get('edit/{id}',['as'=>'.edit','middleware'=>['ability:super-admin|account,manage-certificate-index','auth'],'uses'=>'ManageCertificateController@edit']);

    Route::post('update/{id}',['as'=>'.update','middleware'=>['ability:super-admin|account,manage-certificate-index','auth'],'uses'=>'ManageCertificateController@update']);

     Route::get('delete/{id}',['as'=>'.delete','middleware'=>['ability:super-admin|account,manage-certificate-index','auth'],'uses'=>'ManageCertificateController@delete']);
});
/*Front END Group*/
Route::group(['prefix'=>'frontdesk/',                'as'=>'frontdesk', 'namespace'=>'FrontDesk\\'],function(){
  //VISITOR
  Route::get('visitor',['as'=>'.visitor','middleware' =>['ability:super-admin|account,visitor-book','auth'], 'uses'=>'VisitorController@index']);
  Route::get('visitor/add',['as'=>'.visitor.add','middleware' =>['ability:super-admin|account,visitor-book','auth'], 'uses'=>'VisitorController@add']);
  Route::post('visitor/store',['as'=>'.visitor.store','middleware' =>['ability:super-admin|account,visitor-book','auth'], 'uses'=>'VisitorController@store']);
  Route::get('visitor/edit/{id}',['as'=>'.visitor.edit','middleware' =>['ability:super-admin|account,visitor-book','auth'], 'uses'=>'VisitorController@edit']);
  Route::post('visitor/edit/{id}',['as'=>'.visitor.edit','middleware' =>['ability:super-admin|account,visitor-book','auth'], 'uses'=>'VisitorController@edit']);
  Route::get('visitor/delete/{id}',['as'=>'.visitor.delete','middleware' =>['ability:super-admin|account,visitor-book','auth'], 'uses'=>'VisitorController@delete']);
  //CALL LOG
  Route::get('callLog',['as'=>'.callLog','middleware' =>['ability:super-admin|account,call-log','auth'], 'uses'=>'CallLogController@index']);
  Route::get('callLog/add',['as'=>'.callLog.add','middleware' =>['ability:super-admin|account,call-log','auth'], 'uses'=>'CallLogController@add']);
  Route::post('callLog/store',['as'=>'.callLog.store','middleware' =>['ability:super-admin|account,call-log','auth'], 'uses'=>'CallLogController@store']);
  Route::get('callLog/edit/{id}',['as'=>'.callLog.edit','middleware' =>['ability:super-admin|account,call-log','auth'], 'uses'=>'CallLogController@edit']);

  Route::post('callLog/edit/{id}',['as'=>'.callLog.edit','middleware' =>['ability:super-admin|account,call-log','auth'], 'uses'=>'CallLogController@edit']);
  Route::get('callLog/delete/{id}',['as'=>'.callLog.delete','middleware' =>['ability:super-admin|account,call-log','auth'], 'uses'=>'CallLogController@delete']);
  Route::get('callLog/view/{id}',['as'=>'.callLog.view','middleware' =>['ability:super-admin|account,call-log','auth'], 'uses'=>'CallLogController@view']);
  Route::post('callLog/addFollowUp/{id}',['as'=>'.callLog.addFollowUp','middleware'=>'auth', 'uses'=>'CallLogController@addFollowUp']);
  Route::post('callLog/updateFollowUp/{id}',['as'=>'.callLog.updateFollowUp','middleware'=>'auth', 'uses'=>'CallLogController@editFollowUp']);
  Route::get('callLog/followUpHistory/edit/{id}',['as'=>'.callLog.followUpHistory.edit','middleware'=>'auth', 'uses'=>'CallLogController@editFollowUp']);
  Route::get('callLog/followUpHistory/delete/{id}',['as'=>'.callLog.followUpHistory.delete','middleware'=>'auth', 'uses'=>'CallLogController@deleteFollowUp']);
   Route::get('callLog/followUpHistory/changeStatus/{log}/{history}/{st}',['as'=>'.callLog.followUpHistory.changeStatus','middleware'=>'auth', 'uses'=>'CallLogController@changeFollowUpStatus']);
   //POSTAL DISPATCH
   Route::get('postal',['as'=>'.postal','middleware' =>['ability:super-admin|account,postal-list','auth'], 'uses'=>'PostalController@index']);
   Route::get('postal/dispatch',['as'=>'.postal.dispatch','middleware' =>['ability:super-admin|account,postal-dispatch','auth'], 'uses'=>'PostalController@postaldispatch']);
    Route::post('postal/dispatch',['as'=>'.postal.dispatch','middleware' =>['ability:super-admin|account,postal-dispatch','auth'], 'uses'=>'PostalController@postaldispatch']);
    Route::get('postal/receive',['as'=>'.postal.receive','middleware' =>['ability:super-admin|account,postal-received','auth'], 'uses'=>'PostalController@postalreceive']);
    Route::post('postal/receive',['as'=>'.postal.receive','middleware' =>['ability:super-admin|account,postal-received','auth'], 'uses'=>'PostalController@postalreceive']);
    Route::get('postal/edit/{id}',['as'=>'.postal.edit','middleware' =>['ability:super-admin|account,postal-list','auth'], 'uses'=>'PostalController@edit']);
    Route::post('postal/edit/{id}',['as'=>'.postal.edit','middleware' =>['ability:super-admin|account,postal-list','auth'], 'uses'=>'PostalController@edit']);
    Route::get('postal/delete/{id}',['as'=>'.postal.delete','middleware' =>['ability:super-admin|account,postal-list','auth'], 'uses'=>'PostalController@delete']);
   /*Complain*/
    Route::get('complain',['as'=>'.complain','middleware' =>['ability:super-admin|account,complaint-list','auth'], 'uses'=>'ComplainController@index']);
  Route::get('complain/add',['as'=>'.complain.add','middleware' =>['ability:super-admin|account,complaint-list','auth'], 'uses'=>'ComplainController@add']);
  Route::post('complain/store',['as'=>'.complain.store','middleware' =>['ability:super-admin|account,complaint-list','auth'], 'uses'=>'ComplainController@store']);
  Route::get('complain/edit/{id}',['as'=>'.complain.edit','middleware' =>['ability:super-admin|account,complaint-list','auth'], 'uses'=>'ComplainController@edit']);
  Route::post('complain/edit/{id}',['as'=>'.complain.edit','middleware' =>['ability:super-admin|account,complaint-list','auth'], 'uses'=>'ComplainController@edit']);
  Route::get('complain/delete/{id}',['as'=>'.complain.delete','middleware' =>['ability:super-admin|account,complaint-list','auth'], 'uses'=>'ComplainController@delete']);
   Route::get('complain/changeStatus/{id}/{status}',['as'=>'.complain.changeStatus','middleware' =>['ability:super-admin|account,complaint-list','auth'], 'uses'=>'ComplainController@changeStatus']);
});
/* Time Table Route Grouping */
Route::group(['prefix'=>'timetable/',                 'as'=>'timetable',                 'namespace'=>'TimeTable\\'],function(){
    /*Add Subject */
     Route::get('',                  ['as'=>'',              'middleware'=>['ability:super-admin|account,weekly-timetable','auth'],       'uses'=>'TimeTableController@index']);
     Route::get('daily',                  ['as'=>'.daily',              'middleware'=>['ability:super-admin|account,day-wise-timetable','auth'],       'uses'=>'TimeTableController@daily_index']);
     Route::get('edit/{id}',                  ['as'=>'.edit',              'middleware'=>['ability:super-admin|account,day-wise-timetable','auth'],       'uses'=>'TimeTableController@edit']);

      Route::post('update/{id}',                  ['as'=>'.update',              'middleware'=>['ability:super-admin|account,day-wise-timetable','auth'],       'uses'=>'TimeTableController@update']);

    Route::get('subject',                  ['as'=>'.subject', 'middleware'=>['ability:super-admin|account,assign-subject-classcourse-master-index','auth'],       'uses'=>'SubjectController@index']);
    Route::post('subject/store',                  ['as'=>'.subject.store',              'middleware'=>'auth',['ability:super-admin'],       'uses'=>'SubjectController@store']);
    /*assign subject to class/course*/
      Route::get('subject/edit/{courseid}/{sectionid}',                  ['as'=>'.subject.edit',              'middleware'=>'auth',['ability:super-admin'],       'uses'=>'SubjectController@edit']);
      Route::post('subject/edit/{courseid}/{sectionid}',                  ['as'=>'.subject.edit',              'middleware'=>'auth',['ability:super-admin'],       'uses'=>'SubjectController@edit']);
      /*assign subject to class/course*/
     Route::get('subject/delete/{id}',                  ['as'=>'.subject.delete',              'middleware'=>'auth',['ability:super-admin'],       'uses'=>'SubjectController@delete']);

      Route::get('assign',                  ['as'=>'.assign',              'middleware'=>['ability:super-admin|account,subject-master-index','auth'],       'uses'=>'AssignSubjectController@index']);
    Route::post('assign/store',                  ['as'=>'.assign.store',              'middleware'=>['ability:super-admin|account,subject-master-index','auth'],       'uses'=>'AssignSubjectController@store']);
     Route::get('assign/edit/{id}',                  ['as'=>'.assign.edit',              'middleware'=>['ability:super-admin|account,subject-master-index','auth'],       'uses'=>'AssignSubjectController@edit']);
      Route::post('assign/edit/{id}',                  ['as'=>'.assign.edit',              'middleware'=>['ability:super-admin|account,subject-master-index','auth'],       'uses'=>'AssignSubjectController@edit']);
     Route::get('assign/delete/{id}',                  ['as'=>'.assign.delete',              'middleware'=>['ability:super-admin|account,subject-master-index','auth'],       'uses'=>'AssignSubjectController@delete']);
     Route::post('add',                  ['as'=>'.add',              'middleware'=>['ability:super-admin|account,subject-master-index','auth'],       'uses'=>'TimeTableController@add']);
     
   
});

/*Guardian User Route group*/
Route::group(['prefix' => 'user-guardian/',          'as' => 'user-guardian',       'namespace' => 'UserGuardian\\'], function () {
    /*user-student route group*/
    Route::get('',                              ['as' => '',       'middleware' =>'auth',              'uses' => 'HomeController@index']);
    Route::get('profile',                       ['as' => '.profile',    'middleware' =>'auth',         'uses' => 'HomeController@profile']);
    Route::post('{id}/password',                ['as' => '.password',   'middleware' =>'auth',         'uses' => 'HomeController@password']);
    Route::get('notice',                        ['as' => '.notice',     'middleware' =>'auth',         'uses' => 'HomeController@notice']);

    //guardian's student wise summary routes
    Route::get('students',                      ['as' => '.students',       'middleware' =>'auth',         'uses' => 'HomeController@students']);
    Route::get('students/{id}/profile',         ['as' => '.students.profile',  'middleware' =>'auth',      'uses' => 'HomeController@studentProfile']);
    Route::get('students/{id}/fees',            ['as' => '.students.fees',    'middleware' =>'auth',       'uses' => 'HomeController@fees']);
    Route::get('students/{id}/library',         ['as' => '.students.library',   'middleware' =>'auth',     'uses' => 'HomeController@library']);
    Route::get('students/{id}/attendance',      ['as' => '.students.attendance', 'middleware' =>'auth',    'uses' => 'HomeController@attendance']);
    Route::get('students/{id}/hostel',          ['as' => '.students.hostel',   'middleware' =>'auth',      'uses' => 'HomeController@hostel']);
    Route::get('students/{id}/transport',       ['as' => '.students.transport',  'middleware' =>'auth',    'uses' => 'HomeController@transport']);
    Route::get('students/{id}/subject',         ['as' => '.students.subject',    'middleware' =>'auth',    'uses' => 'HomeController@subject']);
    Route::get('students/{id}/download',        ['as' => '.students.download',   'middleware' =>'auth',    'uses' => 'HomeController@download']);

    Route::get('students/{id}/exams',                                                            ['as' => '.students.exams',               'uses' => 'HomeController@exams']);
    Route::get('students/{id}/exam-schedule/{year}/{month}/{exam}/{faculty}/{semester}',         ['as' => '.students.exam-schedule',       'uses' => 'HomeController@examSchedule']);
    Route::get('students/{id}/exam-admit-card/{year}/{month}/{exam}/{faculty}/{semester}',       ['as' => '.students.exam-admit-card',     'uses' => 'HomeController@admitCard']);
    Route::get('students/{id}/exam-score/{year}/{month}/{exam}/{faculty}/{semester}',            ['as' => '.students.exam-score',          'uses' => 'HomeController@examScore']);

    Route::get('students/{id}assignment/',                                                 ['as' => '.students.assignment',      'middleware' =>'auth',   'uses' => 'HomeController@assignment']);
    Route::get('students/{id}/assignment/answer/{assignment}/{answer}/view',               ['as' => '.students.assignment.answer.view',    'middleware' =>'auth',   'uses' => 'HomeController@viewAssignmentAnswer']);

});

/*user-Staff route group*/
Route::group(['prefix' => 'user-staff/',          'as' => 'user-staff',       'namespace' => 'UserStaff\\'], function () {
    Route::get('',                      ['as' => '',        'middleware' =>'auth',             'uses' => 'HomeController@index']);
    Route::post('{id}/password',        ['as' => '.password',    'middleware' =>'auth',        'uses' => 'HomeController@password']);
    Route::get('payroll',               ['as' => '.payroll',     'middleware' =>'auth',         'uses' => 'HomeController@payroll']);
    Route::get('library',               ['as' => '.library',    'middleware' =>'auth',         'uses' => 'HomeController@library']);
    Route::get('attendance',            ['as' => '.attendance',   'middleware' =>'auth',       'uses' => 'HomeController@attendance']);
    Route::get('exam-score',            ['as' => '.exam-score',   'middleware' =>'auth',       'uses' => 'HomeController@examScore']);
    Route::get('hostel',                ['as' => '.hostel',     'middleware' =>'auth',         'uses' => 'HomeController@hostel']);
    Route::get('transport',             ['as' => '.transport',   'middleware' =>'auth',        'uses' => 'HomeController@transport']);
    Route::get('transport/view/{id}',             ['as' => '.transport.view',   'middleware' =>'auth',        'uses' => 'HomeController@transportDetail']);
    Route::get('subject',               ['as' => '.subject',     'middleware' =>'auth',        'uses' => 'HomeController@subject']);
    Route::get('notice',                ['as' => '.notice',      'middleware' =>'auth',        'uses' => 'HomeController@notice']);

    /*Student Attendance*/
    Route::get('student-attendance/add',               ['as' => '.student-attendance.add',                'middleware' => 'auth',['ability:super-admin,student-attendance-add'],             'uses' => 'HomeController@addStudentAttendance']);
    Route::post('student-attendance/store',            ['as' => '.student-attendance.store',              'middleware' => 'auth',['ability:super-admin,student-attendance-add'],             'uses' => 'HomeController@storeStudentAttendance']);
    Route::post('student-html',                        ['as' => '.student-attendance.student-html',                                                                                   'uses' => 'HomeController@studentHtmlRow']);

    /*Assignment*/
    Route::get('assignment',                           ['as' => '.assignment',                   'middleware' => 'auth',['ability:super-admin,assignment-index'],               'uses' => 'HomeController@assignment']);
    Route::get('timetable',         ['as' => '.timetable',       'middleware' =>'auth',        'uses' => 'HomeController@viewTimetable']);
    Route::get('live_class',         ['as' => '.live_class',       'middleware' =>'auth',        'uses' => 'HomeController@live_class']);
    Route::get('/host_class/{meeting_id}/{meeting_name}','HomeController@host_class')->name('.host_class');
      Route::get('live_class/join_class/{meeting_id}/{meeting_name}',         ['as' => '.live_class.join_class',       'middleware' =>'auth',        'uses' => 'HomeController@join_class']);

});



/*Students Grouping*/
Route::group(['prefix' => 'student/',                                   'as' => 'student',                                     'namespace' => 'Student\\'], function () {

    Route::get('',                          ['as' => '',                         'middleware' => ['ability:super-admin|account,student-detail','auth'],                  'uses' => 'StudentController@index']);
    Route::get('registration',              ['as' => '.registration',            'middleware' => ['ability:super-admin|account,student-registration','auth'],           'uses' => 'StudentController@registration']);
    Route::get('registration/{id}',              ['as' => '.addregistration',            'middleware' => ['ability:super-admin|account,student-registration','auth'],           'uses' => 'StudentController@addregistration']);
    Route::post('register',                 ['as' => '.register',                'middleware' => ['ability:super-admin|account,student-registration','auth'],               'uses' => 'StudentController@register']);
    Route::get('{id}/view',                 ['as' => '.view',                    'middleware' => ['ability:super-admin|account,student-view','auth'],                   'uses' => 'StudentController@view']);
    Route::get('{id}/edit',                 ['as' => '.edit',                    'middleware' => ['ability:super-admin|account,student-edit','auth'],                   'uses' => 'StudentController@edit']);
    Route::post('{id}/update',              ['as' => '.update',                  'middleware' =>['ability:super-admin|account,student-edit','auth'],                    'uses' => 'StudentController@update']);
    Route::get('{id}/delete',               ['as' => '.delete',                  'middleware' => ['ability:super-admin|account,student-delete','auth'],                 'uses' => 'StudentController@delete']);
    Route::post('bulk-action',              ['as' => '.bulk-action',             'middleware' => ['ability:super-admin|account,student-bulk-action','auth'],            'uses' => 'StudentController@bulkAction']);
    Route::get('{id}/active',               ['as' => '.active',                  'middleware' => ['ability:super-admin|account,student-active','auth'],                 'uses' => 'StudentController@Active']);
    Route::get('{id}/in-active',            ['as' => '.in-active',               'middleware' => ['ability:super-admin|account,student-in-active','auth'],              'uses' => 'StudentController@inActive']);
    Route::post('find-semester',            ['as' => '.find-semester',                                                                                   'uses' => 'StudentController@findSemester']);
     Route::post('find-students',            ['as' => '.find-students',                                                                                   'uses' => 'StudentController@findStudents']);
    
    Route::post('academicInfo-html',        ['as' => '.academicInfo-html',                                                                               'uses' => 'StudentController@academicInfoHtml']);
    Route::get('{id}/delete-academicInfo',  ['as' => '.delete-academicInfo',     'middleware' => ['ability:super-admin|account,student-delete-academic-info','auth'],   'uses' => 'StudentController@deleteAcademicInfo']);
    Route::get('guardian-name-autocomplete',  ['as' => '.guardian-name-autocomplete',                                                                     'uses' => 'StudentController@guardianNameAutocomplete']);
    Route::post('guardianInfo-html',            ['as' => '.guardianInfo-html',                                                                            'uses' => 'StudentController@guardianInfo']);

    Route::get('import',                      ['as' => '.import',             'middleware' =>['ability:super-admin|account,student-import-student','auth'],           'uses' => 'StudentController@importStudent']);
    Route::post('import',                     ['as' => '.bulk.import',        'middleware' =>['ability:super-admin|account,student-import-student','auth'],             'uses' => 'StudentController@handleImportStudent']);

    Route::get('import-student',                      ['as' => '.import-student',             'middleware' =>['ability:super-admin|account,student-import-student','auth'],           'uses' => 'StudentController@importStudentNew']);

    Route::post('import-student',                      ['as' => '.import-student',             'middleware' =>['ability:super-admin|account,student-import-student','auth'],           'uses' => 'StudentController@importStudentNew']);

    Route::get('import-fee',                      ['as' => '.import-fee',             'middleware' =>'auth', ['ability:super-admin|account,student-add'],           'uses' => 'StudentController@importFee']);
    Route::post('import-assign-fee',                      ['as' => '.import-assign-fee',             'middleware' =>'auth', ['ability:super-admin|account,student-add'],           'uses' => 'StudentController@importAssignFee']);
    Route::post('import-student-fee',                      ['as' => '.import-student-fee',             'middleware' =>'auth', ['ability:super-admin|account,student-add'],           'uses' => 'StudentController@importStudentFee']);
    Route::post('import-course',                      ['as' => '.import-course',             'middleware' =>'auth', ['ability:super-admin|account,student-add'],           'uses' => 'StudentController@importCourse']);
    /*Student transfer */
   /*Transfer Student Routes*/
    Route::get('transfer-student',                  ['as' => '.transfer-student',                  'middleware' =>['ability:super-admin|account,  transfer-student','auth'],      'uses' => 'TransferStudentController@index']);
    Route::post('transfer-student',                  ['as' => '.transfer-student',                  'middleware' =>['ability:super-admin|account, transfer-student','auth'],      'uses' => 'TransferStudentController@index']);
    Route::post('find-course',                    ['as' => '.find-course',                                                              'uses' => 'TransferStudentController@findCourse']); /*LOAD COURSE AJAX*/
    Route::post('find-assigned-fee',                    ['as' => '.find-assigned-fee',                                                              'uses' => 'TransferStudentController@getAssignedFee']);
     Route::get('{std_id}/{session}/transferStudent',              ['as' => '.transferStudent',               'middleware' => 'auth',['ability:super-admin|account,student-transfer'],      'uses' => 'TransferStudentController@transferStudent']);
    Route::post('transfering-student',              ['as' => '.transfering-student',               'middleware' => 'auth',['ability:super-admin|account,student-transfer'],      'uses' => 'TransferStudentController@transfer']);
    /*Promote Student Routes*/
     Route::get('transfer',                  ['as' => '.transfer',                  'middleware' =>['ability:super-admin|account,promote-student','auth'],      'uses' => 'StudentController@transfer']);
    Route::post('transfering',              ['as' => '.transfering',               'middleware' => 'auth',['ability:super-admin|account,promote-student','auth'],      'uses' => 'StudentController@transfering']);

     Route::get('transfer/bulk',                  ['as' => '.transfer.bulk',                  'middleware' =>['ability:super-admin|account,promote-student-bulk','auth'],      'uses' => 'StudentController@transfer_bulk']);
     Route::post('transfer/bulk',                  ['as' => '.transfer.bulk',                  'middleware' =>['ability:super-admin|account,promote-student-bulk','auth'],      'uses' => 'StudentController@transfer_bulk_store']);
    /*Student login access*/
    Route::post('user/create',             ['as' => '.user.create',                  'middleware' => 'auth',['ability:super-admin|account,user-add'],                    'uses' => 'StudentController@createUser']);
    Route::post('{id}/user/update',        ['as' => '.user.update',                  'middleware' =>'auth', ['ability:super-admin|account,user-edit'],                   'uses' => 'StudentController@updateUser']);
    Route::get('{id}/user/active',         ['as' => '.user.active',                  'middleware' => 'auth',['ability:super-admin|account,user-active'],                 'uses' => 'StudentController@activeUser']);
    Route::get('{id}/user/in-active',      ['as' => '.user.in-active',               'middleware' => 'auth',['ability:super-admin|account,user-in-active'],              'uses' => 'StudentController@inActiveUser']);
    Route::get('{id}/user/delete',         ['as' => '.user.delete',                  'middleware' => 'auth',['ability:super-admin|account,user-delete'],                 'uses' => 'StudentController@deleteUser']);

    /*Guardian login access*/
    Route::post('guardian/user/create',             ['as' => '.guardian.user.create',                  'middleware' => 'auth',['ability:super-admin|account,user-add'],                    'uses' => 'StudentController@createUser']);
    Route::post('guardian/{id}/user/update',        ['as' => '.guardian.user.update',                  'middleware' => 'auth',['ability:super-admin|account,user-edit'],                   'uses' => 'StudentController@updateUser']);
    Route::get('guardian/{id}/user/active',         ['as' => '.guardian.user.active',                  'middleware' =>'auth', ['ability:super-admin|account,user-active'],                 'uses' => 'StudentController@activeUser']);
    Route::get('guardian/{id}/user/in-active',      ['as' => '.guardian.user.in-active',               'middleware' => 'auth',['ability:super-admin|account,user-in-active'],              'uses' => 'StudentController@inActiveUser']);
    Route::get('guardian/{id}/user/delete',         ['as' => '.guardian.user.delete',                  'middleware' =>'auth', ['ability:super-admin|account,user-delete'],                 'uses' => 'StudentController@deleteUser']);
    
    Route::get('generatepassword',                  ['as' => '.generatepassword',                       'middleware' =>['ability:super-admin|account,student-login-detail-generate-password','auth'],        'uses' => 'StudentController@GeneratePassword']);
    Route::post('generatepassword',                 ['as' => '.generatepassword',                       'middleware' =>['ability:super-admin|account,student-login-detail-generate-password','auth'],        'uses' => 'StudentController@GeneratePassword']);
    Route::get('logindetail',                       ['as' => '.logindetail',                            'middleware' => ['ability:super-admin|account,student-view-login-detail','auth'],        'uses' => 'StudentController@LoginDetailList']);
    
    /*Student Document Upload*/
    Route::get('document',                      ['as' => '.document',                'middleware' =>'auth', ['ability:super-admin|account,student-document-index'],      'uses' => 'DocumentController@index']);
    Route::post('document/store',               ['as' => '.document.store',          'middleware' =>'auth', ['ability:super-admin|account,student-document-add'],      'uses' => 'DocumentController@store']);
    Route::get('document/{id}/edit',            ['as' => '.document.edit',           'middleware' =>'auth', ['ability:super-admin|account,student-document-edit'],      'uses' => 'DocumentController@edit']);
    Route::post('document/{id}/update',         ['as' => '.document.update',         'middleware' => 'auth',['ability:super-admin|account,student-document-edit'],      'uses' => 'DocumentController@update']);
    Route::get('document/{id}/delete',          ['as' => '.document.delete',         'middleware' =>'auth', ['ability:super-admin|account,student-document-delete'],      'uses' => 'DocumentController@delete']);
    Route::get('document/{id}/active',          ['as' => '.document.active',         'middleware' =>'auth', ['ability:super-admin|account,student-document-active'],      'uses' => 'DocumentController@Active']);
    Route::get('document/{id}/in-active',       ['as' => '.document.in-active',      'middleware' => 'auth',['ability:super-admin|account,student-document-in-active'],      'uses' => 'DocumentController@inActive']);
    Route::post('document/bulk-action',         ['as' => '.document.bulk-action',    'middleware' => 'auth',['ability:super-admin|account,student-document-bulk-action'],      'uses' => 'DocumentController@bulkAction']);

    /*Student Notes Creating*/
    Route::get('note',                          ['as' => '.note',                'middleware' => 'auth',['ability:super-admin|account,student-note-index'],      'uses' => 'NoteController@index']);
    Route::post('note/store',                   ['as' => '.note.store',          'middleware' => 'auth',['ability:super-admin|account,student-note-add'],      'uses' => 'NoteController@store']);
    Route::get('note/{id}/edit',                ['as' => '.note.edit',           'middleware' => 'auth',['ability:super-admin|account,student-note-edit'],      'uses' => 'NoteController@edit']);
    Route::post('note/{id}/update',             ['as' => '.note.update',         'middleware' => 'auth',['ability:super-admin|account,student-note-edit'],      'uses' => 'NoteController@update']);
    Route::get('note/{id}/delete',              ['as' => '.note.delete',         'middleware' => 'auth',['ability:super-admin|account,student-note-delete'],      'uses' => 'NoteController@delete']);
    Route::get('note/{id}/active',              ['as' => '.note.active',         'middleware' => 'auth',['ability:super-admin|account,student-note-active'],      'uses' => 'NoteController@Active']);
    Route::get('note/{id}/in-active',           ['as' => '.note.in-active',      'middleware' => 'auth',['ability:super-admin|account,student-note-in-active'],      'uses' => 'NoteController@inActive']);
    Route::post('note/bulk-action',             ['as' => '.note.bulk-action',    'middleware' =>'auth', ['ability:super-admin|account,student-note-bulk-action'],      'uses' => 'NoteController@bulkAction']);

    /*bulk edit student*/
    Route::get('bulk_edit_student',                      ['as' => '.bulk_edit_student',             'middleware' =>['ability:super-admin|account,bulk_edit_student','auth'],           'uses' => 'StudentController@BulkEditStudent']);
    Route::post('bulk_edit_student',                     ['as' => '.bulk_edit_student',             'middleware' =>['ability:super-admin|account,bulk_edit_student','auth'],           'uses' => 'StudentController@BulkEditStudent']);
    Route::post('bulk_edit_student/edit',                ['as' => '.bulk_edit_student.edit',        'middleware' =>['ability:super-admin|account,bulk_edit_student','auth'],           'uses' => 'StudentController@Bulk_update_student_data']);
    /*bulk edit student*/
    
    
    /*sibling route*/
     Route::POST('delete-sibling',            ['as' => '.delete-sibling',                                                                                   'uses' => 'StudentController@DeleteSibling']);
    /*sibling route*/
    
    
    
    /* School Leaving Certificate */
    Route::get('{id}/leave',                 ['as' => '.leave',                    'middleware' => ['ability:super-admin|account,school-leaving-certificate','auth'],                   'uses' => 'StudentController@leave']);

    Route::post('{id}/leave_store',                 ['as' => '.leave_store',                    'middleware' => ['ability:super-admin|account,school-leaving-certificate','auth'],                   'uses' => 'StudentController@leave_store']);
    
    Route::get('{id}/leave_print',                 ['as' => '.leave_print',                    'middleware' => ['ability:super-admin|account,school-leaving-certificate','auth'],                   'uses' => 'StudentController@leave_print']);
    
    
    Route::get('{id}/yearly_payment_report',                 ['as' => '.yearly_payment_report',                    'middleware' => ['ability:super-admin|account,yearly-payment-report','auth'],                   'uses' => 'StudentController@yearly_payment_report']);

});

/*Staff Grouping*/
Route::group(['prefix' => 'staff/',                                     'as' => 'staff',                                       'namespace' => 'Staff\\'], function () {
    /*Staff Routes*/
    Route::get('',                          ['as' => '',                    'middleware' =>['ability:super-admin|account,staff-index','auth'],        'uses' => 'StaffController@index']);
    Route::get('add',                       ['as' => '.add',                'middleware' =>['ability:super-admin|account,staff-add','auth'],          'uses' => 'StaffController@add']);
    Route::post('store',                    ['as' => '.store',              'middleware' =>['ability:super-admin|account,staff-add','auth'],          'uses' => 'StaffController@store']);

    Route::get('{id}/edit',                 ['as' => '.edit',               'middleware' =>['ability:super-admin|account,staff-edit','auth'],         'uses' => 'StaffController@edit']);

    Route::post('{id}/update',              ['as' => '.update',             'middleware' =>['ability:super-admin|account,staff-edit','auth'],         'uses' => 'StaffController@update']);

    Route::get('{id}/view',                 ['as' => '.view',               'middleware' =>['ability:super-admin|account,staff-view','auth'],         'uses' => 'StaffController@view']);

    Route::get('{id}/delete',               ['as' => '.delete',             'middleware' =>['ability:super-admin|account,staff-delete','auth'],       'uses' => 'StaffController@delete']);

    Route::get('{id}/active',               ['as' => '.active',             'middleware' =>['ability:super-admin|account,staff-active','auth'],       'uses' => 'StaffController@Active']);

    Route::get('{id}/in-active',            ['as' => '.in-active',          'middleware' => ['ability:super-admin|account,staff-in-active','auth'],    'uses' => 'StaffController@inActive']);

    Route::post('bulk-action',              ['as' => '.bulk-action',        'middleware' =>['ability:super-admin|account,staff-bulk-action','auth'],  'uses' => 'StaffController@bulkAction']);

    Route::get('import',                      ['as' => '.import',             'middleware' =>['ability:super-admin|account,staff-add','auth'],           'uses' => 'StaffController@importStaff']);
    Route::post('import',                     ['as' => '.bulk.import',        'middleware' =>['ability:super-admin|account,staff-add','auth'],             'uses' => 'StaffController@handleImportStaff']);


    /*Staff login access*/
    Route::post('user/create',             ['as' => '.user.create',                  'middleware' => 'auth',['ability:super-admin|account,user-add'],                    'uses' => 'StaffController@createUser']);
    Route::post('{id}/user/update',        ['as' => '.user.update',                  'middleware' => 'auth',['ability:super-admin|account,user-edit'],                   'uses' => 'StaffController@updateUser']);
    Route::get('{id}/user/active',         ['as' => '.user.active',                  'middleware' => 'auth',['ability:super-admin|account,user-active'],                 'uses' => 'StaffController@activeUser']);
    Route::get('{id}/user/in-active',      ['as' => '.user.in-active',               'middleware' => 'auth',['ability:super-admin|account,user-in-active'],              'uses' => 'StaffController@inActiveUser']);
    Route::get('{id}/user/delete',         ['as' => '.user.delete',                  'middleware' =>'auth', ['ability:super-admin|account,user-delete'],                 'uses' => 'StaffController@deleteUser']);

    /*Staff Document Upload*/
    Route::get('document',                  ['as' => '.document',               'middleware' => 'auth',['ability:super-admin|account,staff-document-index'],       'uses' => 'DocumentController@index']);
    Route::post('document/store',           ['as' => '.document.store',         'middleware' => 'auth',['ability:super-admin|account,staff-document-add'],         'uses' => 'DocumentController@store']);
    Route::get('document/{id}/edit',        ['as' => '.document.edit',          'middleware' =>'auth', ['ability:super-admin|account,staff-document-edit'],        'uses' => 'DocumentController@edit']);
    Route::post('document/{id}/update',     ['as' => '.document.update',        'middleware' => 'auth',['ability:super-admin|account,staff-document-edit'],        'uses' => 'DocumentController@update']);
    Route::get('document/{id}/delete',      ['as' => '.document.delete',        'middleware' =>'auth', ['ability:super-admin|account,staff-document-delete'],      'uses' => 'DocumentController@delete']);
    Route::get('document/{id}/active',      ['as' => '.document.active',        'middleware' => 'auth',['ability:super-admin|account,staff-document-active'],      'uses' => 'DocumentController@Active']);
    Route::get('document/{id}/in-active',   ['as' => '.document.in-active',     'middleware' =>'auth', ['ability:super-admin|account,staff-document-in-active'],   'uses' => 'DocumentController@inActive']);
    Route::post('document/bulk-action',     ['as' => '.document.bulk-action',   'middleware' =>'auth', ['ability:super-admin|account,staff-document-bulk-action'], 'uses' => 'DocumentController@bulkAction']);

    /*Staff Notes Creating*/
    Route::get('note',                      ['as' => '.note',                'middleware' => 'auth',['ability:super-admin|account,staff-note-index'],      'uses' => 'NoteController@index']);
    Route::post('note/store',               ['as' => '.note.store',          'middleware' => 'auth',['ability:super-admin|account,staff-note-add'],        'uses' => 'NoteController@store']);
    Route::get('note/{id}/edit',            ['as' => '.note.edit',           'middleware' => 'auth',['ability:super-admin|account,staff-note-edit'],       'uses' => 'NoteController@edit']);
    Route::post('note/{id}/update',         ['as' => '.note.update',         'middleware' =>'auth', ['ability:super-admin|account,staff-note-edit'],       'uses' => 'NoteController@update']);
    Route::get('note/{id}/delete',          ['as' => '.note.delete',         'middleware' => 'auth',['ability:super-admin|account,staff-note-delete'],     'uses' => 'NoteController@delete']);
    Route::get('note/{id}/active',          ['as' => '.note.active',         'middleware' => 'auth',['ability:super-admin|account,staff-note-acctive'],    'uses' => 'NoteController@Active']);
    Route::get('note/{id}/in-active',       ['as' => '.note.in-active',      'middleware' => 'auth',['ability:super-admin|account,staff-note-in-active'],   'uses' => 'NoteController@inActive']);
    Route::post('note/bulk-action',         ['as' => '.note.bulk-action',    'middleware' => 'auth',['ability:super-admin|account,staff-note-bulk-action'], 'uses' => 'NoteController@bulkAction']);

    /*Staff Designation*/
    Route::get('designation',                   ['as' => '.designation',                'middleware' =>'auth', ['ability:super-admin|account,staff-designation-index'],        'uses' => 'DesignationController@index']);
    Route::post('designation/store',            ['as' => '.designation.store',          'middleware' =>'auth', ['ability:super-admin|account,staff-designation-add'],          'uses' => 'DesignationController@store']);
    Route::get('designation/{id}/edit',         ['as' => '.designation.edit',           'middleware' =>'auth', ['ability:super-admin|account,staff-designation-edit'],         'uses' => 'DesignationController@edit']);
    Route::post('designation/{id}/update',      ['as' => '.designation.update',         'middleware' =>'auth', ['ability:super-admin|account,staff-designation-edit'],       'uses' => 'DesignationController@update']);
    Route::get('designation/{id}/delete',       ['as' => '.designation.delete',         'middleware' =>'auth', ['ability:super-admin|account,staff-designation-delete'],       'uses' => 'DesignationController@delete']);
    Route::get('designation/{id}/active',       ['as' => '.designation.active',         'middleware' =>'auth', ['ability:super-admin|account,staff-designation-active'],       'uses' => 'DesignationController@Active']);
    Route::get('designation/{id}/in-active',    ['as' => '.designation.in-active',      'middleware' =>'auth', ['ability:super-admin|account,staff-designation-in-active'],    'uses' => 'DesignationController@inActive']);
    Route::post('designation/bulk-action',      ['as' => '.designation.bulk-action',    'middleware' =>'auth', ['ability:super-admin|account,staff-designation-bulk-action'],  'uses' => 'DesignationController@bulkAction']);
});

/*Accounting Grouping*/
Route::group(['prefix' => 'account/',                                   'as' => 'account.', 'namespace' => 'Account\\'], function () {
 /*Fees Group*/
    /*Balance Fees*/
    Route::get('fees/',                    ['as' => 'fees',                          'middleware' => 'auth',['ability:super-admin|account,fees-index'],            'uses' => 'Fees\FeesBaseController@index']);
    Route::get('fees/balance',             ['as' => 'fees.balance',                  'middleware' => 'auth',['ability:super-admin|account,fees-balance'],          'uses' => 'Fees\FeesBaseController@balance']);

    /*Fee Head*/
    Route::get('fees/head',                    ['as' => 'fees.head',                  'middleware' =>['ability:super-admin|account,fees-head-index','auth'],                  'uses' => 'Fees\FeesHeadController@index']);
    Route::post('fees/head/store',             ['as' => 'fees.head.store',            'middleware' => ['ability:super-admin|account,fees-head-add','auth'],                    'uses' => 'Fees\FeesHeadController@store']);
    Route::get('fees/head/{id}/edit',          ['as' => 'fees.head.edit',             'middleware' =>['ability:super-admin|account,fees-head-edit','auth'],                   'uses' => 'Fees\FeesHeadController@edit']);
    Route::post('fees/head/{id}/update',       ['as' => 'fees.head.update',           'middleware' =>['ability:super-admin|account,fees-head-edit','auth'],                   'uses' => 'Fees\FeesHeadController@update']);
    Route::get('fees/head/{id}/delete',        ['as' => 'fees.head.delete',           'middleware' =>['ability:super-admin|account,fees-head-delete','auth'],                 'uses' => 'Fees\FeesHeadController@delete']);
    Route::get('fees/head/{id}/active',        ['as' => 'fees.head.active',           'middleware' =>['ability:super-admin|account,fees-head-active','auth'],                 'uses' => 'Fees\FeesHeadController@Active']);
    Route::get('fees/head/{id}/in-active',     ['as' => 'fees.head.in-active',        'middleware' => ['ability:super-admin|account,fees-head-in-active','auth'],              'uses' => 'Fees\FeesHeadController@inActive']);
    Route::post('fees/head/bulk-action',       ['as' => 'fees.head.bulk-action',      'middleware' => ['ability:super-admin|account,fees-head-bulk-action','auth'],            'uses' => 'Fees\FeesHeadController@bulkAction']);

    /*Fee Master*/
    Route::get('fees/master',                    ['as' => 'fees.master',                  'middleware' =>'auth', ['ability:super-admin|account,fees-master-index'],            'uses' => 'Fees\FeesMasterController@index']);
    Route::get('fees/master/add',                ['as' => 'fees.master.add',              'middleware' =>'auth', ['ability:super-admin|account,fees-master-add'],              'uses' => 'Fees\FeesMasterController@add']);
    Route::post('fees/master/store',             ['as' => 'fees.master.store',            'middleware' => 'auth',['ability:super-admin|account,fees-master-add'],              'uses' => 'Fees\FeesMasterController@store']);
    Route::get('fees/master/{id}/edit',          ['as' => 'fees.master.edit',             'middleware' => 'auth',['ability:super-admin|account,fees-master-edit'],             'uses' => 'Fees\FeesMasterController@edit']);
    Route::post('fees/master/{id}/update',       ['as' => 'fees.master.update',           'middleware' => 'auth',['ability:super-admin|account,fees-master-edit'],             'uses' => 'Fees\FeesMasterController@update']);
    Route::get('fees/master/{id}/delete',        ['as' => 'fees.master.delete',           'middleware' => 'auth',['ability:super-admin|account,fees-master-delete'],           'uses' => 'Fees\FeesMasterController@delete']);
    Route::post('fees/master/bulk-action',       ['as' => 'fees.master.bulk-action',      'middleware' =>'auth', ['ability:super-admin|account,fees-master-bulk-action'],      'uses' => 'Fees\FeesMasterController@bulkAction']);
    Route::get('fees/master/{id}/active',        ['as' => 'fees.master.active',           'middleware' => 'auth',['ability:super-admin|account,fees-master-active'],           'uses' => 'Fees\FeesMasterController@Active']);
    Route::get('fees/master/{id}/in-active',     ['as' => 'fees.master.in-active',        'middleware' => 'auth',['ability:super-admin|account,fees-master-in-active'],        'uses' => 'Fees\FeesMasterController@inActive']);
    Route::post('fees/master/fee-html',          ['as' => 'fees.master.fee-html',                                                                               'uses' => 'Fees\FeesMasterController@feeHtmlRow']);

    /*Collect Fee */
    Route::get('fees/collection',                    ['as' => 'fees.collection',                  'middleware' => 'auth',['ability:super-admin|account,fees-collection-index'],            'uses' => 'Fees\FeesCollectionController@index']);
    Route::get('fees/collection/{id}/add',           ['as' => 'fees.collection.add',              'middleware' => 'auth',['ability:super-admin|account,fees-collection-add'],              'uses' => 'Fees\FeesCollectionController@add']);
    Route::post('fees/collection/store',             ['as' => 'fees.collection.store',            'middleware' => 'auth',['ability:super-admin|account,fees-collection-add'],              'uses' => 'Fees\FeesCollectionController@store']);
    Route::get('fees/collection/{id}/view',          ['as' => 'fees.collection.view',             'middleware' => 'auth',['ability:super-admin|account,fees-collection-view'],             'uses' => 'Fees\FeesCollectionController@view']);
    Route::get('fees/collection/{id}/delete',        ['as' => 'fees.collection.delete',           'middleware' => 'auth',['ability:super-admin|account,fees-collection-delete'],           'uses' => 'Fees\FeesCollectionController@delete']);

    /*online payment*/
    Route::post('fees/pay-with-stripe',               ['as' => 'fees.stripePayment',                 'middleware' => 'auth',['ability:super-admin|account,fees-payment-stripe-payment'],     'uses' => 'Fees\Payment\StripePaymentController@stripePayment']);

    Route::post('fees/payu-form',                 ['as' => 'fees.payu-form',                'middleware' =>'auth', ['ability:super-admin|account,fees-payment-payu-payment'],     'uses' => 'PaymentController@payuForm']);
    Route::post('fees/pay-with-payumoney/success',     ['as' => 'fees.payumoney.success',             'middleware' =>'auth', ['ability:super-admin|account,fees-payment-payumoney-payment'],     'uses' => 'Fees\Payment\PayumoneyPaymentController@payumoneyPaymentSuccess']);
    Route::post('fees/pay-with-payumoney/failure',     ['as' => 'fees.payumoney.failure',              'middleware' => 'auth',['ability:super-admin|account,fees-payment-payumoney-payment'],     'uses' => 'Fees\Payment\PayumoneyPaymentController@payumoneyPaymentFailure']);
    //Route::post('fees/pay-with-khalti',               ['as' => 'fees.khaltiPayment',                 'middleware' => ['ability:super-admin|account,fees-payment-khalti-payment'],     'uses' => 'Fees\FeesCollectionController@khaltiPayment']);

    Route::post('fees/pesapal-form',                 ['as' => 'fees.pesapal-form',                      'middleware' => 'auth',['ability:super-admin|account,fees-payment-pesapal'],              'uses' => 'Fees\Payment\PesapalPaymentController@pesapalForm']);
    Route::post('fees/pay-with-pesapal',             ['as' => 'fees.pesapal',                           'middleware' =>'auth', ['ability:super-admin|account,fees-payment-pesapal'],             'uses' => 'Fees\Payment\PesapalPaymentController@payment']);
    Route::get('fees/pesapal/donepayment',           ['as' => 'fees.pesapal.paymentsuccess',            'middleware' =>'auth', ['ability:super-admin|account,fees-payment-pesapal'],             'uses' => 'Fees\Payment\PesapalPaymentController@paymentsuccess']);
    Route::get('fees/pesapal/paymentconfirmation',   ['as' => 'fees.pesapal.paymentconfirmation',       'middleware' =>'auth', ['ability:super-admin|account,fees-payment-pesapal'],             'uses' => 'Fees\Payment\PesapalPaymentController@paymentconfirmation']);

    /*Payroll Group*/
    /*Balance Payroll*/
    Route::get('payroll/balance',                    ['as' => 'payroll.balance',           'middleware' => ['ability:super-admin|account,payroll-balance'],                   'uses' => 'Payroll\PayrollBaseController@index']);

    /*Payroll Head*/
    Route::get('payroll/head',                    ['as' => 'payroll.head',                  'middleware' => ['ability:super-admin|account,payroll-head-index','auth'],             'uses' => 'Payroll\PayrollHeadController@index']);
    Route::post('payroll/head/store',             ['as' => 'payroll.head.store',            'middleware' => ['ability:super-admin|account,payroll-head-add','auth'],               'uses' => 'Payroll\PayrollHeadController@store']);
    Route::get('payroll/head/{id}/edit',          ['as' => 'payroll.head.edit',             'middleware' =>['ability:super-admin|account,payroll-head-edit','auth'],              'uses' => 'Payroll\PayrollHeadController@edit']);
    Route::post('payroll/head/{id}/update',       ['as' => 'payroll.head.update',           'middleware' => ['ability:super-admin|account,payroll-head-edit'],              'uses' => 'Payroll\PayrollHeadController@update','auth']);
    Route::get('payroll/head/{id}/delete',        ['as' => 'payroll.head.delete',           'middleware' =>['ability:super-admin|account,payroll-head-delete'],            'uses' => 'Payroll\PayrollHeadController@delete','auth']);
    Route::get('payroll/head/{id}/active',        ['as' => 'payroll.head.active',           'middleware' =>['ability:super-admin|account,payroll-head-active'],            'uses' => 'Payroll\PayrollHeadController@Active','auth']);
    Route::get('payroll/head/{id}/in-active',     ['as' => 'payroll.head.in-active',        'middleware' =>['ability:super-admin|account,payroll-head-in-active'],         'uses' => 'Payroll\PayrollHeadController@inActive','auth']);
    Route::post('payroll/head/bulk-action',       ['as' => 'payroll.head.bulk-action',      'middleware' =>['ability:super-admin|account,payroll-head-bulk-action'],       'uses' => 'Payroll\PayrollHeadController@bulkAction','auth']);

    /*Payroll Master*/
    Route::get('payroll/master',                    ['as' => 'payroll.master',                  'middleware' =>['ability:super-admin|account,payroll-master-index','auth'],           'uses' => 'Payroll\PayrollMasterController@index']);
    Route::get('payroll/master/add',                ['as' => 'payroll.master.add',              'middleware' =>['ability:super-admin|account,payroll-master-add','auth'],             'uses' => 'Payroll\PayrollMasterController@add']);

    Route::post('payroll/master/store',             ['as' => 'payroll.master.store',            'middleware' =>['ability:super-admin|account,payroll-master-add','auth'],             'uses' => 'Payroll\PayrollMasterController@store']);

    Route::get('payroll/master/{id}/edit',          ['as' => 'payroll.master.edit',             'middleware' =>['ability:super-admin|account,payroll-master-edit','auth'],            'uses' => 'Payroll\PayrollMasterController@edit']);

    Route::post('payroll/master/{id}/update',       ['as' => 'payroll.master.update',           'middleware' =>['ability:super-admin|account,payroll-master-edit','auth'],            'uses' => 'Payroll\PayrollMasterController@update']);


    Route::get('payroll/master/{id}/delete',        ['as' => 'payroll.master.delete',           'middleware' =>['ability:super-admin|account,payroll-master-delete','auth'],          'uses' => 'Payroll\PayrollMasterController@delete']);
    Route::post('payroll/master/bulk-action',       ['as' => 'payroll.master.bulk-action',      'middleware' => ['ability:super-admin|account,payroll-master-bulk-action','auth'],     'uses' => 'Payroll\PayrollMasterController@bulkAction']);

    Route::get('payroll/master/{id}/active',        ['as' => 'payroll.master.active',           'middleware' =>['ability:super-admin|account,payroll-master-active','auth'],          'uses' => 'Payroll\PayrollMasterController@Active']);

    Route::get('payroll/master/{id}/in-active',     ['as' => 'payroll.master.in-active',        'middleware' =>['ability:super-admin|account,payroll-master-in-active','auth'],       'uses' => 'Payroll\PayrollMasterController@inActive']);
    Route::post('payroll/master/payroll-html',       ['as' => 'payroll.master.payroll-html',                                                                            'uses' => 'Payroll\PayrollMasterController@payrollHtmlRow']);

    /*Pay Salary*/
    Route::get('salary/payment',                    ['as' => 'salary.payment',                  'middleware' =>['ability:super-admin|account,salary-payment-index','auth'],           'uses' => 'Payroll\SalaryPayController@index']);

    Route::get('salary/payment/{id}/add',           ['as' => 'salary.payment.add',              'middleware' =>['ability:super-admin|account,salary-payment-add','auth'],             'uses' => 'Payroll\SalaryPayController@add']);

    Route::post('salary/payment/store',             ['as' => 'salary.payment.store',            'middleware' =>['ability:super-admin|account,salary-payment-add','auth'],             'uses' => 'Payroll\SalaryPayController@store']);

    Route::get('salary/payment/{id}/view',          ['as' => 'salary.payment.view',             'middleware' =>['ability:super-admin|account,salary-payment-view','auth'],            'uses' => 'Payroll\SalaryPayController@view']);

    Route::get('salary/payment/{id}/delete',        ['as' => 'salary.payment.delete',           'middleware' =>['ability:super-admin|account,salary-payment-delete','auth'],          'uses' => 'Payroll\SalaryPayController@delete']);

    /*Transaction Head*/
    Route::get('transaction-head',                    ['as' => 'transaction-head',                  'middleware' =>['ability:super-admin|account,transaction-head-index','auth'],         'uses' => 'Transaction\TransactionHeadController@index']);
    Route::post('transaction-head/store',             ['as' => 'transaction-head.store',            'middleware' =>['ability:super-admin|account,transaction-head-add','auth'],           'uses' => 'Transaction\TransactionHeadController@store']);
    Route::get('transaction-head/{id}/edit',          ['as' => 'transaction-head.edit',             'middleware' =>['ability:super-admin|account,transaction-head-edit'],          'uses' => 'Transaction\TransactionHeadController@edit','auth']);
    Route::post('transaction-head/{id}/update',       ['as' => 'transaction-head.update',           'middleware' =>['ability:super-admin|account,transaction-head-edit','auth'],          'uses' => 'Transaction\TransactionHeadController@update']);

    Route::get('transaction-head/{id}/delete',        ['as' => 'transaction-head.delete',           'middleware' => ['ability:super-admin|account,transaction-head-delete','auth'],        'uses' => 'Transaction\TransactionHeadController@delete']);

    Route::get('transaction-head/{id}/active',        ['as' => 'transaction-head.active',           'middleware' =>['ability:super-admin|account,transaction-head-active','auth'],        'uses' => 'Transaction\TransactionHeadController@Active']);

    Route::get('transaction-head/{id}/in-active',     ['as' => 'transaction-head.in-active',        'middleware' =>['ability:super-admin|account,transaction-head-in-active','auth'],     'uses' => 'Transaction\TransactionHeadController@inActive']);

    Route::post('transaction-head/bulk-action',       ['as' => 'transaction-head.bulk-action',      'middleware' =>['ability:super-admin|account,transaction-head-bulk-action','auth'],   'uses' => 'Transaction\TransactionHeadController@bulkAction']);

    /*Transaction*/
    Route::get('transaction',                    ['as' => 'transaction',                  'middleware' =>['ability:super-admin|account,transaction-index','auth'],                'uses' => 'Transaction\TransactionController@index']);

    Route::get('transaction/add', ['as' => 'transaction.add', 'middleware' =>['ability:super-admin|account,transaction-add','auth'], 'uses' => 'Transaction\TransactionController@new_add']);


    Route::get('transaction/printed/{id}', ['as' => 'transaction.printed', 'middleware' => 'auth', 'uses' => 'Transaction\TransactionController@printed']);

    //Route::get('transaction/printed/{$id}', ['as'=>'transaction.printed', 'middleware'=>'auth', ['ability:super-admin|account,transaction-add'], 'uses'=>'TransactionController@printed']);

    Route::post('transaction/store',             ['as' => 'transaction.store',            'middleware' =>['ability:super-admin|account,transaction-add','auth'],                  'uses' => 'Transaction\TransactionController@new_store']);
    Route::get('transaction/{id}/edit',          ['as' => 'transaction.edit',             'middleware' => ['ability:super-admin|account,transaction-edit','auth'],                 'uses' => 'Transaction\TransactionController@new_edit']);

    Route::post('transaction/{id}/edit',          ['as' => 'transaction.edit',             'middleware' =>['ability:super-admin|account,transaction-edit','auth'],                 'uses' => 'Transaction\TransactionController@new_edit']);

    Route::post('transaction/{id}/update',       ['as' => 'transaction.update',           'middleware' =>['ability:super-admin|account,transaction-edit','auth'],                 'uses' => 'Transaction\TransactionController@update']);

    Route::get('transaction/{id}/delete',        ['as' => 'transaction.delete',           'middleware' => ['ability:super-admin|account,transaction-delete','auth'],               'uses' => 'Transaction\TransactionController@new_delete']);

    Route::post('transaction/bulk-action',       ['as' => 'transaction.bulk-action',      'middleware' =>['ability:super-admin|account,transaction-bulk-action','auth'],          'uses' => 'Transaction\TransactionController@bulkAction']);


    Route::get('transaction/{id}/active',        ['as' => 'transaction.active',           'middleware' =>['ability:super-admin|account,transaction-active','auth'],               'uses' => 'Transaction\TransactionController@Active']);

    Route::get('transaction/{id}/in-active',     ['as' => 'transaction.in-active',        'middleware' =>['ability:super-admin|account,transaction-in-active','auth'],            'uses' => 'Transaction\TransactionController@inActive']);

    Route::post('transaction/tr-html',           ['as' => 'transaction.tr-html',                                                                                    'uses' => 'Transaction\TransactionController@trHtmlRow']);


});

/*Library Grouping*/
Route::group(['prefix' => 'library/',                                   'as' => 'library.',                                    'namespace' => 'Library\\'], function () {

    Route::get('',                          ['as' => '',                        'middleware' =>['ability:super-admin|library,library-index','auth'],           'uses' => 'LibraryBaseController@index']);
    Route::post('issue',                    ['as' => 'issue',                   'middleware' =>['ability:super-admin|library,library-book-issue','auth'],      'uses' => 'LibraryBaseController@issueBook']);
    Route::get('{id}/return',               ['as' => 'return',                  'middleware' =>['ability:super-admin|library,library-book-return','auth'],     'uses' => 'LibraryBaseController@returnBook']);
    Route::get('return-over',               ['as' => 'return-over',             'middleware' =>['ability:super-admin|library,library-return-over','auth'],     'uses' => 'LibraryBaseController@returnOver']);
    Route::get('issue-history',             ['as' => 'issue-history',           'middleware' =>['ability:super-admin|library,library-issue-history','auth'],   'uses' => 'LibraryBaseController@issueHistory']);
    Route::post('book-detail-html',         ['as' => 'book-detail-html',                                                                         'uses' => 'LibraryBaseController@bookDetail']);
    Route::get('book-name-autocomplete',    ['as' => 'book-name-autocomplete',                                                                   'uses' => 'LibraryBaseController@bookNameAutocomplete']);

    /*Book Master*/
    Route::get('book',                          ['as' => 'book',                'middleware' =>['ability:super-admin|library,book-index','auth'],         'uses' => 'BookController@index']);
    Route::get('book/add',                      ['as' => 'book.add',            'middleware' =>['ability:super-admin|library,book-add','auth'],           'uses' => 'BookController@add']);
    Route::post('book/store',                   ['as' => 'book.store',          'middleware' =>['ability:super-admin|librarlibraryy,book-add','auth'],           'uses' => 'BookController@store']);
    Route::get('book/{id}/edit',                ['as' => 'book.edit',           'middleware' =>['ability:super-admin|library,book-edit','auth'],          'uses' => 'BookController@edit']);
    Route::post('book/{id}/update',             ['as' => 'book.update',         'middleware' =>['ability:super-admin|library,book-edit','auth'],          'uses' => 'BookController@update']);

    Route::get('book/import',                      ['as' => 'book.import',            'middleware' => ['ability:super-admin|library,book-add','auth'],           'uses' => 'BookController@importBook']);
    Route::post('book/import',                     ['as' => 'book.bulk.import',        'middleware' =>['ability:super-admin|library,book-add','auth'],             'uses' => 'BookController@handleImportBook']);


    Route::get('book/{id}/view',                ['as' => 'book.view',           'middleware' =>['ability:super-admin|library,book-view','auth'],          'uses' => 'BookController@view']);
    Route::get('book/{id}/delete',              ['as' => 'book.delete',         'middleware' =>['ability:super-admin|library,book-delete','auth'],        'uses' => 'BookController@delete']);
    Route::get('book/{id}/active',              ['as' => 'book.active',         'middleware' =>['ability:super-admin|library,book-active','auth'],        'uses' => 'BookController@Active']);
    Route::get('book/{id}/in-active',           ['as' => 'book.in-active',      'middleware' =>['ability:super-admin|library,book-in-active','auth'],     'uses' => 'BookController@inActive']);
    Route::post('book/bulk-action',             ['as' => 'book.bulk-action',    'middleware' =>['ability:super-admin|library,book-bulk-action','auth'],   'uses' => 'BookController@bulkAction']);

    /*Book Level*/
    Route::post('book/add/copies',                  ['as' => 'book.add.copies',             'middleware' =>['ability:super-admin|library,book-add-copies','auth'],              'uses' => 'BookController@addCopies']);
    Route::get('book/{id}/book-status/{status}',    ['as' => 'book.book-status',            'middleware' =>['ability:super-admin|library,book-status','auth'],                  'uses' => 'BookController@bookStatus']);
    Route::post('book/bulk-copies-delete',          ['as' => 'book.bulk-copies-delete',     'middleware' =>['ability:super-admin|library,book-bulk-copies-delete','auth'],      'uses' => 'BookController@bulkCopiesDelete']);

    /*Books Category Routes*/
    Route::get('book/category',                     ['as' => 'book.category',               'middleware' =>['ability:super-admin|library,book-category-index','auth'],            'uses' => 'BookCategoryController@index']);
    Route::post('book/category/store',              ['as' => 'book.category.store',         'middleware' =>['ability:super-admin|library,book-category-add','auth'],              'uses' => 'BookCategoryController@store']);
    Route::get('book/category/{id}/edit',           ['as' => 'book.category.edit',          'middleware' =>['ability:super-admin|library,book-category-edit','auth'],             'uses' => 'BookCategoryController@edit']);
    Route::post('book/category/{id}/update',        ['as' => 'book.category.update',        'middleware' =>['ability:super-admin|library,book-category-edit','auth'],             'uses' => 'BookCategoryController@update']);
    Route::get('book/category/{id}/delete',         ['as' => 'book.category.delete',        'middleware' =>['ability:super-admin|library,book-category-delete','auth'],           'uses' => 'BookCategoryController@delete']);
    Route::get('book/category/{id}/active',         ['as' => 'book.category.active',        'middleware' =>['ability:super-admin|library,book-category-active','auth'],           'uses' => 'BookCategoryController@Active']);
    Route::get('book/category/{id}/in-active',      ['as' => 'book.category.in-active',     'middleware' =>['ability:super-admin|library,book-category-in-active','auth'],        'uses' => 'BookCategoryController@inActive']);
    Route::post('book/category/bulk-action',        ['as' => 'book.category.bulk-action',   'middleware' =>['ability:super-admin|library,book-category-bulk-action','auth'],      'uses' => 'BookCategoryController@bulkAction']);

    /*Books Category Routes*/
    /*Library Circulations*/
    Route::get('circulation',                       ['as' => 'circulation',                 'middleware' =>['ability:super-admin|library,library-circulation-index','auth'],          'uses' => 'CirculationController@index']);
    Route::post('circulation/store',                ['as' => 'circulation.store',           'middleware' =>['ability:super-admin|library,library-circulation-add','auth'],            'uses' => 'CirculationController@store']);
    Route::get('circulation/{id}/edit',             ['as' => 'circulation.edit',            'middleware' =>['ability:super-admin|library,library-circulation-edit','auth'],           'uses' => 'CirculationController@edit']);
    Route::post('circulation/{id}/update',          ['as' => 'circulation.update',          'middleware' =>['ability:super-admin|library,library-circulation-edit','auth'],           'uses' => 'CirculationController@update']);
    Route::get('circulation/{id}/delete',           ['as' => 'circulation.delete',          'middleware' =>['ability:super-admin|library,library-circulation-delete','auth'],         'uses' => 'CirculationConlibrarytroller@delete']);
    Route::get('circulation/{id}/active',           ['as' => 'circulation.active',          'middleware' =>['ability:super-admin|library,library-circulation-active','auth'],         'uses' => 'CirculationController@Active']);
    Route::get('circulation/{id}/in-active',        ['as' => 'circulation.in-active',       'middleware' => ['ability:super-admin|library,library-circulation-in-active','auth'],      'uses' => 'CirculationController@inActive']);
    Route::post('circulation/bulk-action',          ['as' => 'circulation.bulk-action',     'middleware' =>['ability:super-admin|library,library-circulation-bulk-action','auth'],    'uses' => 'CirculationController@bulkAction']);
     /*Library Circulations*/

    /*Library Member Routes*/
    Route::get('member',                    ['as' => 'member',              'middleware' => ['ability:super-admin|library,library-member-index','auth'],           'uses' => 'MemberController@index']);
    Route::get('member/add',                ['as' => 'member.add',          'middleware' => ['ability:super-admin|library,library-member-add','auth'],             'uses' => 'MemberController@add']);
    Route::post('member/store',             ['as' => 'member.store',        'middleware' => ['ability:super-admin|library,library-member-add','auth'],             'uses' => 'MemberController@store']);
    Route::get('member/{id}/edit',          ['as' => 'member.edit',         'middleware' => ['ability:super-admin|library,library-member-edit','auth'],            'uses' => 'MemberController@edit']);
    Route::post('member/{id}/update',       ['as' => 'member.update',       'middleware' => ['ability:super-admin|library,library-member-edit','auth'],            'uses' => 'MemberController@update']);
    Route::get('member/{id}/delete',        ['as' => 'member.delete',       'middleware' => ['ability:super-admin|library,library-member-delete','auth'],          'uses' => 'MemberController@delete']);
    Route::get('member/{id}/active',        ['as' => 'member.active',       'middleware' => ['ability:super-admin|library,library-member-active','auth'],          'uses' => 'MemberController@Active']);
    Route::get('member/{id}/in-active',     ['as' => 'member.in-active',    'middleware' => ['ability:super-admin|library,library-member-in-active','auth'],       'uses' => 'MemberController@inActive']);
    Route::post('member/bulk-action',       ['as' => 'member.bulk-action',  'middleware' => ['ability:super-admin|library,library-member-bulk-action','auth'],     'uses' => 'MemberController@bulkAction']);

    /*Library Member Staff Routes*/
    /*Staff Member*/
    Route::get('staff',                 ['as' => 'staff',                   'middleware' => ['ability:super-admin|library,library-member-staff','auth'],      'uses' => 'StaffMemberController@staff']);
    Route::get('staff/{id}/view',       ['as' => 'staff.view',              'middleware' => ['ability:super-admin|library,library-member-staff-view','auth'],      'uses' => 'StaffMemberController@staffView']);

    /*Student Member*/
    Route::get('student',                   ['as' => 'student',                 'middleware' => ['ability:super-admin|library,library-member-student','auth'],      'uses' => 'StudentMemberController@student']);
    Route::get('student/{id}/view',         ['as' => 'student.view',            'middleware' => ['ability:super-admin|library,library-member-student-view','auth'],      'uses' => 'StudentMemberController@studentView']);

});




/*Attendance Grouping*/
Route::group(['prefix' => 'attendance/',                                'as' => 'attendance',                                  'namespace' => 'Attendance\\'], function () {
 /*Attendance */
    /*Attendance Master*/
    Route::get('master',                        ['as' => '.master',                 'middleware' => ['ability:super-admin|account,attendance-master-index','auth'],            'uses' => 'AttendanceMasterController@index']);
    Route::get('master/add',                    ['as' => '.master.add',             'middleware' => ['ability:super-admin|account,attendance-master-add','auth'],              'uses' => 'AttendanceMasterController@add']);
    Route::post('master/store',                 ['as' => '.master.store',           'middleware' => ['ability:super-admin|account,attendance-master-add','auth'],              'uses' => 'AttendanceMasterController@store']);
    Route::get('master/{id}/edit',              ['as' => '.master.edit',            'middleware' => ['ability:super-admin|account,attendance-master-edit','auth'],             'uses' => 'AttendanceMasterController@edit']);
    Route::post('master/{id}/update',           ['as' => '.master.update',          'middleware' => ['ability:super-admin|account,attendance-master-edit','auth'],             'uses' => 'AttendanceMasterController@update']);
    Route::get('master/{id}/delete',            ['as' => '.master.delete',          'middleware' => ['ability:super-admin|account,attendance-master-delete','auth'],           'uses' => 'AttendanceMasterController@delete']);
    Route::get('master/{id}/active',            ['as' => '.master.active',          'middleware' => ['ability:super-admin|account,attendance-master-active','auth'],           'uses' => 'AttendanceMasterController@Active']);
    Route::get('master/{id}/in-active',         ['as' => '.master.in-active',       'middleware' => ['ability:super-admin|account,attendance-master-in-active','auth'],        'uses' => 'AttendanceMasterController@inActive']);
    Route::post('master/bulk-action',           ['as' => '.master.bulk-action',     'middleware' => ['ability:super-admin|account,attendance-master-bulk-action','auth'],      'uses' => 'AttendanceMasterController@bulkAction']);
    Route::post('master/month-html',            ['as' => '.master.month-html',                                                                                  'uses' => 'AttendanceMasterController@monthHtmlRow']);

    /*Student Attendance*/
    Route::get('student',                   ['as' => '.student',                    'middleware' => ['ability:super-admin|account,student-attendance-index','auth'],           'uses' => 'StudentAttendanceController@index']);
    Route::get('student/add',               ['as' => '.student.add',                'middleware' => ['ability:super-admin|account,student-attendance-add','auth'],             'uses' => 'StudentAttendanceController@add']);
    Route::post('student/store',            ['as' => '.student.store',              'middleware' => ['ability:super-admin|account,student-attendance-add','auth'],             'uses' => 'StudentAttendanceController@store']);
    Route::get('student/{id}/delete',       ['as' => '.student.delete',             'middleware' => ['ability:super-admin|account,student-attendance-delete','auth'],          'uses' => 'StudentAttendanceController@delete']);
    Route::post('student/bulk-action',      ['as' => '.student.bulk-action',        'middleware' => ['ability:super-admin|account,student-attendance-bulk-action','auth'],     'uses' => 'StudentAttendanceController@bulkAction']);
    Route::post('student-html',             ['as' => '.student-html',                                                                                           'uses' => 'StudentAttendanceController@studentHtmlRow']);

    /*Staff Attendance*/
    Route::get('staff',                         ['as' => '.staff',                  'middleware' => ['ability:super-admin|account,staff-attendance-index','auth'],             'uses' => 'StaffAttendanceController@index']);
    Route::get('staff/add',                     ['as' => '.staff.add',              'middleware' => ['ability:super-admin|account,staff-attendance-add','auth'],               'uses' => 'StaffAttendanceController@add']);
    Route::post('staff/store',                  ['as' => '.staff.store',            'middleware' => ['ability:super-admin|account,staff-attendance-add','auth'],               'uses' => 'StaffAttendanceController@store']);
    Route::get('staff/{id}/delete',             ['as' => '.staff.delete',           'middleware' => ['ability:super-admin|account,staff-attendance-delete','auth'],            'uses' => 'StaffAttendanceController@delete']);
    Route::post('staff/bulk-action',            ['as' => '.staff.bulk-action',      'middleware' => ['ability:super-admin|account,staff-attendance-bulk-action','auth'],       'uses' => 'StaffAttendanceController@bulkAction']);
    Route::post('staff-html',                   ['as' => '.staff-html',                                                                                         'uses' => 'StaffAttendanceController@staffHtmlRow']);
});

/*Exam group */
Route::group(['prefix' => 'exam/',                                      'as' => 'exam',                                         'namespace' => 'Exam\\'], function () {
    /*EXAM SETUP*/
      /*EXAM TERM*/
      Route::get('setup/exam-term',                                  ['as' => '.setup.exam-term',                  'middleware' => ['ability:super-admin|account,exam-term-setup-index','auth'],           'uses' => 'ExamTermController@index']);
      Route::post('setup/exam-term/store',                                  ['as' => '.setup.exam-term.store',                  'middleware' => ['ability:super-admin|account,exam-term-setup-index','auth'],           'uses' => 'ExamTermController@store']);
       Route::post('setup/exam-term/edit/{id}',                                  ['as' => '.setup.exam-term.edit',                  'middleware' => ['ability:super-admin|account,exam-term-setup-index','auth'],           'uses' => 'ExamTermController@edit']);
       Route::get('setup/exam-term/edit/{id}',                                  ['as' => '.setup.exam-term.edit',                  'middleware' => ['ability:super-admin|account,exam-term-setup-index','auth'],           'uses' => 'ExamTermController@edit']);
       Route::get('setup/exam-term/delete/{id}',                                  ['as' => '.setup.exam-term.delete',                  'middleware' => ['ability:super-admin|account,exam-term-setup-index','auth'],           'uses' => 'ExamTermController@delete']);
      /*END EXAM TERM*/
      /*EXAM TYPE*/
      Route::get('setup/exam-type',                                  ['as' => '.setup.exam-type',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ExamTypeController@index']);
      Route::post('setup/exam-type/store',                                  ['as' => '.setup.exam-type.store',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ExamTypeController@store']);
       Route::post('setup/exam-type/edit/{id}',                                  ['as' => '.setup.exam-type.edit',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ExamTypeController@edit']);
       Route::get('setup/exam-type/edit/{id}',                                  ['as' => '.setup.exam-type.edit',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ExamTypeController@edit']);
       Route::get('setup/exam-type/delete/{id}',                                  ['as' => '.setup.exam-type.delete',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ExamTypeController@delete']);
      /*END EXAM TYPE*/
      /*EXAM PAPER TYPE*/
      Route::get('setup/exam-paper',                                  ['as' => '.setup.exam-paper',                  'middleware' => ['ability:super-admin|account,exam-papertype-setup-index','auth'],           'uses' => 'ExamPaperController@index']);
      Route::post('setup/exam-paper/store',                                  ['as' => '.setup.exam-paper.store',                  'middleware' => ['ability:super-admin|account,exam-papertype-setup-index','auth'],           'uses' => 'ExamPaperController@store']);
       Route::post('setup/exam-paper/edit/{id}',                                  ['as' => '.setup.exam-paper.edit',                  'middleware' => ['ability:super-admin|account,exam-papertype-setup-index','auth'],           'uses' => 'ExamPaperController@edit']);
       Route::get('setup/exam-paper/edit/{id}',                                  ['as' => '.setup.exam-paper.edit',                  'middleware' => ['ability:super-admin|account,exam-papertype-setup-index','auth'],           'uses' => 'ExamPaperController@edit']);
       Route::get('setup/exam-paper/delete/{id}',                                  ['as' => '.setup.exam-paper.delete',                  'middleware' => ['ability:super-admin|account,exam-papertype-setup-index','auth'],           'uses' => 'ExamPaperController@delete']);
      /*END PAPER TYPE*/
      /*EXAM QUESTION TYPE*/
      Route::get('setup/question-type',                                  ['as' => '.setup.question-type',                  'middleware' => ['ability:super-admin|account,exam-questiontype-setup-index','auth'],           'uses' => 'ExamQuestionTypeController@index']);
      Route::post('setup/question-type/store',                                  ['as' => '.setup.question-type.store',                  'middleware' => ['ability:super-admin|account,exam-questiontype-setup-index','auth'],           'uses' => 'ExamQuestionTypeController@store']);
       Route::post('setup/question-type/edit/{id}',                                  ['as' => '.setup.question-type.edit',                  'middleware' => ['ability:super-admin|account,exam-questiontype-setup-index','auth'],           'uses' => 'ExamQuestionTypeController@edit']);
       Route::get('setup/question-type/edit/{id}',                                  ['as' => '.setup.question-type.edit',                  'middleware' => ['ability:super-admin|account,exam-questiontype-setup-index','auth'],           'uses' => 'ExamQuestionTypeController@edit']);
       Route::get('setup/question-type/delete/{id}',                                  ['as' => '.setup.question-type.delete',                  'middleware' => ['ability:super-admin|account,exam-questiontype-setup-index','auth'],           'uses' => 'ExamQuestionTypeController@delete']);
      /*END QUESTION TYPE*/
      /*EXAM MODE*/
      Route::get('setup/exam-mode',                                  ['as' => '.setup.exam-mode',                  'middleware' => ['ability:super-admin|account,exam-mode-setup-index','auth'],           'uses' => 'ExamModeController@index']);
      Route::post('setup/exam-mode/store',                                  ['as' => '.setup.exam-mode.store',                  'middleware' => ['ability:super-admin|account,super-admin'],           'uses' => 'ExamModeController@store']);
       Route::post('setup/exam-mode/edit/{id}',                                  ['as' => '.setup.exam-mode.edit',                  'middleware' => ['ability:super-admin|account,super-admin'],           'uses' => 'ExamModeController@edit']);
       Route::get('setup/exam-mode/edit/{id}',                                  ['as' => '.setup.exam-mode.edit',                  'middleware' => ['ability:super-admin|account,super-admin'],           'uses' => 'ExamModeController@edit']);
       Route::get('setup/exam-mode/delete/{id}',                                  ['as' => '.setup.exam-mode.delete',                  'middleware' => ['ability:super-admin|account,super-admin'],           'uses' => 'ExamModeController@delete']);
      /*END MODE*/
      
      /*Result type*/
      Route::get('setup/result-type',                                  ['as' => '.setup.result-type',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ResultTypeController@index']);
      Route::post('setup/result-type/store',                                  ['as' => '.setup.result-type.store',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ResultTypeController@store']);
       Route::post('setup/result-type/edit/{id}',                                  ['as' => '.setup.exam-type.edit',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ResultTypeController@edit']);
       Route::get('setup/result-type/edit/{id}',                                  ['as' => '.setup.result-type.edit',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ResultTypeController@edit']);
       Route::get('setup/result-type/delete/{id}',                                  ['as' => '.setup.result-type.delete',                  'middleware' => ['ability:super-admin|account,exam-type-setup-index','auth'],           'uses' => 'ResultTypeController@delete']);
      /*  Result type*/

      
      
    /*END EXAM SETUP*/

    /*CREATE EXAM*/
      Route::get('create',                                  ['as' => '.create',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamCreateController@index']);
      Route::get('list',                                  ['as' => '.list',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamCreateController@list']);
      
      Route::post('create/store',                                  ['as' => '.create.store',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamCreateController@store_exam']);
       Route::post('create/edit/{id}',                                  ['as' => '.create.edit',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamCreateController@edit_exam']);
       Route::get('create/edit/{id}',                                  ['as' => '.create.edit',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamCreateController@edit_exam']);
       Route::get('create/delete/{id}',                                  ['as' => '.create.delete',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamCreateController@delete_exam']);
       Route::get('create/status/{id}/{status}',                                  ['as' => '.create.status',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamCreateController@change_status']);
       Route::get('create/result_status/{id}/{status}',                                  ['as' => '.create.result_status',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamCreateController@result_status']);
    /*END CREATE EXAM*/
    /*bulk exam status*/
    Route::post('create/bulk-action',       ['as' => '.create.bulk-action',      'middleware' =>['ability:super-admin|account,exam-index','auth'],       'uses' => 'ExamCreateController@BulkAction']);
       /*bulk exam status*/
    /*EXAM ADD QUESTION*/
      Route::get('{exam_id}/add-question',                                  ['as' => '.add-question',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamAddQuestionController@index']);
      Route::post('{exam_id}/add-question/store',                                  ['as' => '.add-question.store',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamAddQuestionController@store_question']);
       Route::post('{exam_id}/add-question/edit/{id}',                                  ['as' => '.add-question.edit',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamAddQuestionController@edit_question']);
       Route::get('{exam_id}/add-question/edit/{id}',                                  ['as' => '.add-question.edit',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamAddQuestionController@edit_question']);
       Route::get('{exam_id}/add-question/delete/{id}',                                  ['as' => '.add-question.delete',                  'middleware' => ['ability:super-admin|account,exam-index','auth'],           'uses' => 'ExamAddQuestionController@delete_question']);
    /*END EXAM ADD QUESTION*/
    /*EXAM REPORT*/
    Route::get('report',                                  ['as' => '.report',                  'middleware' => ['ability:super-admin|account,gen-report-card-index'],           'uses' => 'ExamReportController@index']);
    Route::post('report/generate',                                  ['as' => '.report.generate',                  'middleware' => ['ability:super-admin|account,gen-report-card-index'],           'uses' => 'ExamReportController@generate']);
    Route::get('studentMarkExcel',                                  ['as' => '.studentMarkExcel',                  'middleware' => ['ability:super-admin|account,gen-report-card-index'],           'uses' => 'ExamReportController@studentMarkExcel']);
    Route::get('studentMarkExcel/print',                                  ['as' => '.studentMarkExcel.print',                  'middleware' => ['ability:super-admin|account,gen-report-card-index'],           'uses' => 'ExamReportController@studentMarkExcel']);

    
    /*END EXAM REPORT*/
    /*EXAM ASSESMENT*/
       Route::get('assessment',                                  ['as' => '.assessment',                  'middleware' => ['ability:super-admin|account,mark-assessment-index','auth'],           'uses' => 'ExamAssessmentController@index']);
       Route::get('assessment/add',                                  ['as' => '.assessment.add',                  'middleware' => ['ability:super-admin|account,mark-assessment-index','auth'],           'uses' => 'ExamAssessmentController@add_assessment']);


       Route::post('assessment/store',                                  ['as' => '.assessment.store',                  'middleware' => ['ability:super-admin|account,mark-assessment-index','auth'],           'uses' => 'ExamAssessmentController@store_assessment']);


       Route::get('assessment/view/{exam_id}/{student_id}',['as'=>'.assessment.view','middleware'=>['ability:super-admin|account,mark-assessment-index','auth'],'uses'=>'ExamAssessmentController@view_student_answer']);

       Route::post('assessment/store_student_mark',['as'=>'.assessment.store_student_mark','middleware'=>['ability:super-admin|account,mark-assessment-index','auth'],'uses'=>'ExamAssessmentController@save_exam_mark']);
       Route::post('assessment/save_offline_exam',['as'=>'.assessment.save_offline_exam','middleware'=>['ability:super-admin|account,mark-assessment-index','auth'],'uses'=>'ExamAssessmentController@store_assessment']);
       // Route::post('report/generate',                                  ['as' => '.report.generate',                  'middleware' => ['ability:super-admin|account,super-admin'],           'uses' => 'ExamReportController@generate']);
    /*END EXAM ASSESMENT*/
    /*Exam Types Routes*/
    Route::get('',                                  ['as' => '',                  'middleware' => ['ability:super-admin|account,exam-index'],           'uses' => 'ExamController@index']);
    Route::post('store',                            ['as' => '.store',            'middleware' => ['ability:super-admin|account,exam-add'],             'uses' => 'ExamController@store']);
    Route::get('{id}/edit',                         ['as' => '.edit',             'middleware' => ['ability:super-admin|account,exam-edit'],            'uses' => 'ExamController@edit']);
    Route::post('{id}/update',                      ['as' => '.update',           'middleware' => ['ability:super-admin|account,exam-edit'],            'uses' => 'ExamController@update']);
    Route::get('{id}/delete',                       ['as' => '.delete',           'middleware' => ['ability:super-admin|account,exam-delete'],          'uses' => 'ExamController@delete']);
    Route::get('{id}/active',                       ['as' => '.active',           'middleware' => ['ability:super-admin|account,exam-active'],          'uses' => 'ExamController@Active']);
    Route::get('{id}/in-active',                    ['as' => '.in-active',        'middleware' => ['ability:super-admin|account,exam-in-active'],       'uses' => 'ExamController@inActive']);
    Route::post('bulk-action',                      ['as' => '.bulk-action',      'middleware' => ['ability:super-admin|account,exam-bulk-action'],     'uses' => 'ExamController@bulkAction']);

    /*Exam AdmitCard Routes*/
    Route::get('admit-card',                        ['as' => '.admit-card',         'middleware' => ['ability:super-admin|account,exam-admit-card'],        'uses' => 'ExamController@admitCard']);
    Route::get('routine',                           ['as' => '.routine',            'middleware' => ['ability:super-admin|account,exam-exam-routine'],      'uses' => 'ExamController@examRoutine']);
    Route::get('mark-sheet',                        ['as' => '.mark-sheet',         'middleware' => ['ability:super-admin|account,exam-mark-sheet'],        'uses' => 'ExamController@markSheet']);

    //result publish status
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/result-publish',    ['as' => '.schedule.result-publish',                  'middleware' => ['ability:super-admin|account,exam-result-publish'],                'uses' => 'ExamScheduleController@publish']);
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/result-un-publish', ['as' => '.schedule.result-un-publish',               'middleware' => ['ability:super-admin|account,exam-result-un-publish'],             'uses' => 'ExamScheduleController@unPublish']);

    /*Exam Schedule Routes*/
    Route::get('schedule',                                                      ['as' => '.schedule',                  'middleware' => ['ability:super-admin|account,exam-schedule-index'],                 'uses' => 'ExamScheduleController@index']);
    Route::get('schedule/add',                                                  ['as' => '.schedule.add',              'middleware' => ['ability:super-admin|account,exam-schedule-add'],                   'uses' => 'ExamScheduleController@add']);
    Route::post('schedule/store',                                               ['as' => '.schedule.store',            'middleware' => ['ability:super-admin|account,exam-schedule-add'],                   'uses' => 'ExamScheduleController@store']);
    Route::get('schedule/{id}/edit',                                            ['as' => '.schedule.edit',             'middleware' => ['ability:super-admin|account,exam-schedule-edit'],                  'uses' => 'ExamScheduleController@edit']);
    Route::post('schedule/{id}/update',                                         ['as' => '.schedule.update',           'middleware' => ['ability:super-admin|account,exam-schedule-edit'],                  'uses' => 'ExamScheduleController@update']);
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/delete',    ['as' => '.schedule.delete',           'middleware' => ['ability:super-admin|account,exam-schedule-delete'],                'uses' => 'ExamScheduleController@delete']);
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/active',    ['as' => '.schedule.active',           'middleware' => ['ability:super-admin|account,exam-schedule-active'],                'uses' => 'ExamScheduleController@active']);
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/in-active', ['as' => '.schedule.in-active',        'middleware' => ['ability:super-admin|account,exam-schedule-in-active'],             'uses' => 'ExamScheduleController@inActive']);
    Route::post('schedule/subject-html',                                        ['as' => '.schedule.subject-html',                                                                                  'uses' => 'ExamScheduleController@subjectHtmlRow']);



    /*Exam Schedule Routes*/
    Route::get('mark-ledger',                                                      ['as' => '.mark-ledger',                     'middleware' => ['ability:super-admin|account,exam-mark-ledger-index'],                 'uses' => 'ExamMarkLedgerController@index']);
    Route::get('mark-ledger/add',                                                  ['as' => '.mark-ledger.add',                 'middleware' => ['ability:super-admin|account,exam-mark-ledger-add'],                   'uses' => 'ExamMarkLedgerController@add']);
    Route::post('mark-ledger/store',                                               ['as' => '.mark-ledger.store',               'middleware' => ['ability:super-admin|account,exam-mark-ledger-add'],                   'uses' => 'ExamMarkLedgerController@store']);
    Route::get('mark-ledger/{id}/edit',                                            ['as' => '.mark-ledger.edit',                'middleware' => ['ability:super-admin|account,exam-mark-ledger-edit'],                  'uses' => 'ExamMarkLedgerController@edit']);
    Route::post('mark-ledger/{id}/update',                                         ['as' => '.mark-ledger.update',              'middleware' => ['ability:super-admin|account,exam-mark-ledger-edit'],                  'uses' => 'ExamMarkLedgerController@update']);
    Route::get('mark-ledger/{exam}/{student}/delete',                              ['as' => '.mark-ledger.delete',              'middleware' => ['ability:super-admin|account,exam-mark-ledger-delete'],                'uses' => 'ExamMarkLedgerController@delete']);
    Route::get('mark-ledger/{exam}/{student}/active',                              ['as' => '.mark-ledger.active',              'middleware' => ['ability:super-admin|account,exam-mark-ledger-active'],                'uses' => 'ExamMarkLedgerController@active']);
    Route::get('mark-ledger/{exam}/{student}/in-active',                           ['as' => '.mark-ledger.in-active',           'middleware' => ['ability:super-admin|account,exam-mark-ledger-in-acctive'],            'uses' => 'ExamMarkLedgerController@inActive']);
    Route::post('mark-ledger/find-subject',                                        ['as' => '.mark-ledger.find-subject',                                                                                        'uses' => 'ExamMarkLedgerController@findSubject']);
    Route::post('mark-ledger/student-html',                                        ['as' => '.mark-ledger.student-html',                                                                                        'uses' => 'ExamMarkLedgerController@studentHtmlRow']);
    
    /*Student Remark module*/
    Route::get('studentRemark',                                  ['as' => '.studentRemark',                  'middleware' => ['ability:super-admin|account,mark-assessment-index','auth'],           'uses' => 'StudentRemarkController@index']);
    Route::post('studentRemark',                                  ['as' => '.studentRemark',                  'middleware' => ['ability:super-admin|account,mark-assessment-index','auth'],           'uses' => 'StudentRemarkController@index']);
    Route::post('studentRemark/store',                                  ['as' => '.studentRemark.store',                  'middleware' => ['ability:super-admin|account,mark-assessment-index','auth'],           'uses' => 'StudentRemarkController@store']);
       /*Student Remark module*/
});

/*Hostel Grouping */
Route::group(['prefix' => 'hostel/',                                    'as' => 'hostel',                                       'namespace' => 'Hostel\\'], function () {

    /*Hostel Routes*/
    Route::get('',                   ['as' => '',                  'middleware' => ['ability:super-admin|account,hostel-index'],                'uses' => 'HostelController@index']);
    Route::get('add',                ['as' => '.add',              'middleware' => ['ability:super-admin|account,hostel-add'],                  'uses' => 'HostelController@add']);
    Route::post('store',             ['as' => '.store',            'middleware' => ['ability:super-admin|account,hostel-add'],                  'uses' => 'HostelController@store']);
    Route::get('{id}/edit',          ['as' => '.edit',             'middleware' => ['ability:super-admin|account,hostel-edit'],                 'uses' => 'HostelController@edit']);
    Route::post('{id}/update',       ['as' => '.update',           'middleware' => ['ability:super-admin|account,hostel-edit'],                 'uses' => 'HostelController@update']);
    Route::get('{id}/view',          ['as' => '.view',             'middleware' => ['ability:super-admin|account,hostel-view'],                 'uses' => 'HostelController@view']);
    Route::get('{id}/delete',        ['as' => '.delete',           'middleware' => ['ability:super-admin|account,hostel-delete'],               'uses' => 'HostelController@delete']);
    Route::get('{id}/active',        ['as' => '.active',           'middleware' => ['ability:super-admin|account,hostel-active'],               'uses' => 'HostelController@Active']);
    Route::get('{id}/in-active',     ['as' => '.in-active',        'middleware' => ['ability:super-admin|account,hostel-in-active'],            'uses' => 'HostelController@inActive']);
    Route::post('bulk-action',       ['as' => '.bulk-action',      'middleware' => ['ability:super-admin|account,hostel-bulk-action'],          'uses' => 'HostelController@bulkAction']);
    Route::post('find-rooms',        ['as' => '.find-rooms',                                                                            'uses' => 'HostelController@findRooms']);
    Route::post('find-beds',         ['as' => '.find-beds',                                                                              'uses' => 'HostelController@findBeds']);

    //ADD BLOCK
    
     Route::get('{id}/block',                ['as' => '.block',              'middleware' => ['ability:super-admin|account,block-add'],                  'uses' => 'HostelController@add_block']);
      Route::get('{id}/block/{blockId}',                ['as' => '.block',              'middleware' => ['ability:super-admin|account,block-add'],                  'uses' => 'HostelController@add_block']);
      Route::get('{id}/block/{blockId}/edit',                ['as' => '.block.edit',              'middleware' => ['ability:super-admin|account,block-add'],                  'uses' => 'HostelController@edit_block']);
       Route::get('{id}/block/{blockId}/delete',                ['as' => '.block.delete',              'middleware' => ['ability:super-admin|account,block-add'],                  'uses' => 'HostelController@add_block']);
      Route::post('{id}/block/{blockId}/edit',                ['as' => '.block.edit',              'middleware' => ['ability:super-admin|account,block-add'],                  'uses' => 'HostelController@edit_block']);
      Route::post('{id}/block',                ['as' => '.block',              'middleware' => ['ability:super-admin|account,hostel-add'],                  'uses' => 'HostelController@add_block']);
    
    //Add Floor
      Route::get('{id}/floor',                ['as' => '.floor',              'middleware' => ['ability:super-admin|account,hostel-add'],                  'uses' => 'HostelController@add_floor']);

       Route::post('{id}/floor',                ['as' => '.floor',              'middleware' => ['ability:super-admin|account,hostel-add'],                  'uses' => 'HostelController@add_floor']);
        Route::get('{id}/floor/{floor_id}/edit',                ['as' => '.floor.edit',              'middleware' => ['ability:super-admin|account,hostel-add'],                  'uses' => 'HostelController@edit_floor']);
        Route::post('{id}/floor/{floor_id}/edit',                ['as' => '.floor.edit',              'middleware' => ['ability:super-admin|account,hostel-add'],                  'uses' => 'HostelController@edit_floor']);

        Route::get('{id}/floor/delete/{floorId}',                ['as' => '.floor.delete',              'middleware' => ['ability:super-admin|account,hostel-add'],                  'uses' => 'HostelController@add_floor']);
        Route::post('room/loadFloor',            ['as' => '.room.floor',                                                                            'uses' => 'RoomController@loadFloor']);

        //Fee Collect
       
        Route::get('fee',                          ['as' => '.fee',                       'middleware' => ['ability:super-admin|account,hostel-collect-fees'],                 'uses' => 'HostelFeeController@index']);
         Route::post('fee/collect',                          ['as' => '.fee.collect',                       'middleware' => ['ability:super-admin|account,hostel-fee'],                 'uses' => 'HostelFeeController@store']);
         Route::post('fee/load-student',                          ['as' => '.fee.load-student',                       'middleware' => ['ability:super-admin|account,hostel-fee'],                 'uses' => 'HostelFeeController@loadStudent']);
           Route::post('fee/load-fee',                          ['as' => '.fee.load-fee',                       'middleware' => ['ability:super-admin|account,hostel-fee'],                 'uses' => 'HostelFeeController@loadFee']);
           Route::get('print/{id}',             ['as' => '.collect.print',     
         'uses' => 'HostelFeeController@collectReceipt']);

    /*Rooms Level*/
    Route::get('{id}/room/add',                         ['as' => '.room.add',                   'middleware' => ['ability:super-admin|account,room-add'],               'uses' => 'RoomController@index']);
    Route::post('{id}/room/add',                         ['as' => '.room.add',                   'middleware' => ['ability:super-admin|account,room-add'],               'uses' => 'RoomController@add']);
    Route::get('{id}/room/{roomId}/edit',                         ['as' => '.room.edit',                   'middleware' => ['ability:super-admin|account,room-add'],               'uses' => 'RoomController@update']);
    Route::post('{id}/room/{roomId}/edit',                         ['as' => '.room.edit',                   'middleware' => ['ability:super-admin|account,room-add'],               'uses' => 'RoomController@update']);
    Route::get('{id}/room/{roomId}/delete',                         ['as' => '.room.delete',                   'middleware' => ['ability:super-admin|account,room-add'],               'uses' => 'RoomController@delete']);

    Route::get('room/{id}/view',                    ['as' => '.room.view',                  'middleware' => ['ability:super-admin|account,room-view'],              'uses' => 'RoomController@view']);
    Route::post('room/{id}/update',                 ['as' => '.room.update',                'middleware' => ['ability:super-admin|account,room-edit'],              'uses' => 'RoomController@update']);
    Route::get('room/{id}/delete',                  ['as' => '.room.delete',                'middleware' => ['ability:super-admin|account,room-delete'],            'uses' => 'RoomController@delete']);
    Route::get('room/{id}/active',                  ['as' => '.room.active',                'middleware' => ['ability:super-admin|account,room-active'],            'uses' => 'RoomController@Active']);
    Route::get('room/{id}/in-active',               ['as' => '.room.in-active',             'middleware' => ['ability:super-admin|account,room-in-active'],         'uses' => 'RoomController@InActive']);
    Route::post('room/bulk-rooms',                  ['as' => '.room.bulk-rooms',            'middleware' => ['ability:super-admin|account,room-bulk-action'],       'uses' => 'RoomController@bulkAction']);

    /*Bed*/
     Route::get('{id}/bed',                          ['as' => '.bed',                'middleware' => ['ability:super-admin|account,beds'],                'uses' => 'BedController@index']);
    Route::post('{id}/bed/add',                          ['as' => '.bed.add',                'middleware' => ['ability:super-admin|account,bed-add'],                'uses' => 'BedController@addBeds']);
    Route::get('{id}/bed/{bedId}/edit',                         ['as' => '.bed.edit',                   'middleware' => ['ability:super-admin|account,bed-edit'],               'uses' => 'BedController@update']);
    Route::post('{id}/bed/{bedId}/edit',                         ['as' => '.bed.edit',                   'middleware' => ['ability:super-admin|account,room-add'],               'uses' => 'BedController@update']);
    Route::get('{id}/bed/{bedId}/delete',                         ['as' => '.bed.delete',                   'middleware' => ['ability:super-admin|account,room-add'],               'uses' => 'BedController@delete']);
    Route::get('bed/{id}/bed-status/{status}',      ['as' => '.bed.bed-status',         'middleware' => ['ability:super-admin|account,bed-status'],             'uses' => 'BedController@bedStatus']);
    // Route::get('bed/{id}/delete',                   ['as' => '.bed.delete',             'middleware' => ['ability:super-admin|account,bed-delete'],             'uses' => 'BedController@delete']);
    Route::get('bed/{id}/active',                   ['as' => '.bed.active',             'middleware' => ['ability:super-admin|account,bed-active'],             'uses' => 'BedController@Active']);
    Route::get('bed/{id}/in-active',                ['as' => '.bed.in-active',          'middleware' => ['ability:super-admin|account,bed-in-active'],          'uses' => 'BedController@InActive']);
    Route::post('bed/bulk-beds',                    ['as' => '.bed.bulk-beds',          'middleware' => ['ability:super-admin|account,bed-bulk-action'],        'uses' => 'BedController@bulkAction']);

    /*Room Types Routes*/
    Route::get('room-type',                    ['as' => '.room-type',                  'middleware' => ['ability:super-admin|account,room-type-index'],             'uses' => 'RoomTypeController@index']);
    Route::post('room-type/store',             ['as' => '.room-type.store',            'middleware' => ['ability:super-admin|account,room-type-add'],               'uses' => 'RoomTypeController@store']);
    Route::get('room-type/{id}/edit',          ['as' => '.room-type.edit',             'middleware' => ['ability:super-admin|account,room-type-edit'],              'uses' => 'RoomTypeController@edit']);
    Route::post('room-type/{id}/update',       ['as' => '.room-type.update',           'middleware' => ['ability:super-admin|account,room-type-edit'],              'uses' => 'RoomTypeController@update']);
    Route::get('room-type/{id}/delete',        ['as' => '.room-type.delete',           'middleware' => ['ability:super-admin|account,room-type-delete'],            'uses' => 'RoomTypeController@delete']);
    Route::get('room-type/{id}/active',        ['as' => '.room-type.active',           'middleware' => ['ability:super-admin|account,room-type-active'],            'uses' => 'RoomTypeController@Active']);
    Route::get('room-type/{id}/in-active',     ['as' => '.room-type.in-active',        'middleware' => ['ability:super-admin|account,room-type-in-active'],         'uses' => 'RoomTypeController@inActive']);
    Route::post('room-type/bulk-action',       ['as' => '.room-type.bulk-action',      'middleware' => ['ability:super-admin|account,room-type-bulk-action'],       'uses' => 'RoomTypeController@bulkAction']);

    /*Hostel Resident Routes*/
    Route::get('resident',                    ['as' => '.resident',                     'middleware' => ['ability:super-admin|account,hostel-resident-index'],                'uses' => 'ResidentController@index']);
    Route::get('resident/add',                ['as' => '.resident.add',                 'middleware' => ['ability:super-admin|account,hostel-registration'],                  'uses' => 'ResidentController@add']);
    Route::post('resident/store',             ['as' => '.resident.store',               'middleware' => ['ability:super-admin|account,hostel-registration'],                  'uses' => 'ResidentController@store']);
    Route::get('resident/{id}/edit',          ['as' => '.resident.edit',                'middleware' => ['ability:super-admin|account,resident-edit'],                 'uses' => 'ResidentController@edit']);
    Route::post('resident/{id}/update',       ['as' => '.resident.update',              'middleware' => ['ability:super-admin|account,resident-edit'],                 'uses' => 'ResidentController@update']);
    Route::get('resident/{id}/delete',        ['as' => '.resident.delete',              'middleware' => ['ability:super-admin|account,resident-delete'],               'uses' => 'ResidentController@delete']);
    Route::post('resident/bulk-action',       ['as' => '.resident.bulk-action',         'middleware' => ['ability:super-admin|account,resident-bulk-action'],          'uses' => 'ResidentController@bulkAction']);
    Route::post('resident/renew',             ['as' => '.resident.renew',               'middleware' => ['ability:super-admin|account,resident-renew'],                 'uses' => 'ResidentController@renew']);
    Route::get('resident/{id}/leave',         ['as' => '.resident.leave',               'middleware' => ['ability:super-admin|account,resident-leave'],                 'uses' => 'ResidentController@leave']);
    Route::post('resident/shift',             ['as' => '.resident.shift',               'middleware' => ['ability:super-admin|account,resident-shift'],                 'uses' => 'ResidentController@shift']);
    Route::get('resident/history',            ['as' => '.resident.history',             'middleware' => ['ability:super-admin|account,hostel-occupation-history'],               'uses' => 'ResidentController@history']);
    /*For Search and Listing Room & Bed*/
    
    Route::post('resident/load-student',        ['as' => '.resident.load-student',          'middleware' => ['ability:super-admin|account,resident'],                       'uses' => 'ResidentController@loadStudent']);
    Route::post('resident/find-block',        ['as' => '.resident.find-block',          'middleware' => ['ability:super-admin|account,resident'],                       'uses' => 'ResidentController@findBlock']);
     Route::post('resident/find-floor',        ['as' => '.resident.find-floor',          'middleware' => ['ability:super-admin|account,resident'],                       'uses' => 'ResidentController@findFloor']);
     Route::post('resident/find-rooms',        ['as' => '.resident.find-rooms',          'middleware' => ['ability:super-admin|account,resident'],                       'uses' => 'ResidentController@findRooms']);
     Route::post('resident/find-bed',        ['as' => '.resident.find-bed',          'middleware' => ['ability:super-admin|account,resident'],                       'uses' => 'ResidentController@findBed']);
     Route::post('resident/find-rate',        ['as' => '.resident.find-rate',          'middleware' => ['ability:super-admin|account,resident'],                       'uses' => 'ResidentController@findRate']);
    Route::post('find-rooms',        ['as' => '.bed.find-rooms',          'middleware' => ['ability:super-admin|account,resident'],                       'uses' => 'BedController@findRooms']);
    Route::post('find-beds',         ['as' => '.find-beds',           'middleware' => ['ability:super-admin|account,resident'],                       'uses' => 'Bed@findBeds']);

/*Food & Meal*/
    /*Food Schedule Routes*/
    Route::get('food',                          ['as' => '.food',                       'middleware' => ['ability:super-admin|account,food-meal-schedule-index'],                 'uses' => 'FoodController@index']);
    Route::post('food/store',                   ['as' => '.food.store',                 'middleware' => ['ability:super-admin|account,food-add'],                   'uses' => 'FoodController@store']);
    Route::get('food/{id}/edit',                ['as' => '.food.edit',                  'middleware' => ['ability:super-admin|account,food-edit'],                  'uses' => 'FoodController@edit']);
    Route::post('food/{id}/update',             ['as' => '.food.update',                'middleware' => ['ability:super-admin|account,food-edit'],                  'uses' => 'FoodController@update']);
    Route::get('food/{id}/delete',              ['as' => '.food.delete',                'middleware' => ['ability:super-admin|account,food-delete'],                'uses' => 'FoodController@delete']);
    Route::get('food/{id}/active',              ['as' => '.food.active',                'middleware' => ['ability:super-admin|account,food-active'],                'uses' => 'FoodController@Active']);
    Route::get('food/{id}/in-active',           ['as' => '.food.in-active',             'middleware' => ['ability:super-admin|account,food-in-active'],             'uses' => 'FoodController@inActive']);
    Route::post('food/bulk-action',             ['as' => '.food.bulk-action',           'middleware' => ['ability:super-admin|account,food-bulk-action'],           'uses' => 'FoodController@bulkAction']);
    Route::post('food/food-html',               ['as' => '.food.food-html',                                                                                 'uses' => 'FoodController@foodHtmlRow']);
    Route::get('food-name-autocomplete',        ['as' => '.food-name-autocomplete',                                                                         'uses' => 'FoodController@foodNameAutocomplete']);

    /*Food Category Routes*/
    Route::get('food/category',                    ['as' => '.food.category',                  'middleware' => ['ability:super-admin|account,food-category-index'],             'uses' => 'FoodCategoryController@index']);
    Route::post('food/category/store',             ['as' => '.food.category.store',            'middleware' => ['ability:super-admin|account,food-category-add'],               'uses' => 'FoodCategoryController@store']);
    Route::get('food/category/{id}/edit',          ['as' => '.food.category.edit',             'middleware' => ['ability:super-admin|account,food-category-edit'],              'uses' => 'FoodCategoryController@edit']);
    Route::post('food/category/{id}/update',       ['as' => '.food.category.update',           'middleware' => ['ability:super-admin|account,food-category-edit'],              'uses' => 'FoodCategoryController@update']);
    Route::get('food/category/{id}/delete',        ['as' => '.food.category.delete',           'middleware' => ['ability:super-admin|account,food-category-delete'],            'uses' => 'FoodCategoryController@delete']);
    Route::get('food/category/{id}/active',        ['as' => '.food.category.active',           'middleware' => ['ability:super-admin|account,food-category-active'],            'uses' => 'FoodCategoryController@Active']);
    Route::get('food/category/{id}/in-active',     ['as' => '.food.category.in-active',        'middleware' => ['ability:super-admin|account,food-category-in-active'],         'uses' => 'FoodCategoryController@inActive']);
    Route::post('food/category/bulk-action',       ['as' => '.food.category.bulk-action',      'middleware' => ['ability:super-admin|account,food-category-bulk-action'],       'uses' => 'FoodCategoryController@bulkAction']);

    /*Food Item Routes*/
    Route::get('food/item',                    ['as' => '.food.item',                          'middleware' => ['ability:super-admin|account,food-index'],             'uses' => 'FoodItemController@index']);
    Route::post('food/item/store',             ['as' => '.food.item.store',                    'middleware' => ['ability:super-admin|account,food-item-add'],               'uses' => 'FoodItemController@store']);
    Route::get('food/item/{id}/edit',          ['as' => '.food.item.edit',                     'middleware' => ['ability:super-admin|account,food-item-edit'],              'uses' => 'FoodItemController@edit']);
    Route::post('food/item/{id}/update',       ['as' => '.food.item.update',                   'middleware' => ['ability:super-admin|account,food-item-edit'],              'uses' => 'FoodItemController@update']);
    Route::get('food/item/{id}/delete',        ['as' => '.food.item.delete',                   'middleware' => ['ability:super-admin|account,food-item-delete'],            'uses' => 'FoodItemController@delete']);
    Route::get('food/item/{id}/active',        ['as' => '.food.item.active',                   'middleware' => ['ability:super-admin|account,food-item-active'],            'uses' => 'FoodItemController@Active']);
    Route::get('food/item/{id}/in-active',     ['as' => '.food.item.in-active',                'middleware' => ['ability:super-admin|account,food-item-in-active'],         'uses' => 'FoodItemController@inActive']);
    Route::post('food/item/bulk-action',       ['as' => '.food.item.bulk-action',              'middleware' => ['ability:super-admin|account,food-item-bulk-action'],       'uses' => 'FoodItemController@bulkAction']);

    /*Food Eating Time Routes*/
    Route::get('food/eating-time',                    ['as' => '.food.eating-time',                  'middleware' => ['ability:super-admin|account,eating-time-index'],             'uses' => 'EatingTimeController@index']);
    Route::post('food/eating-time/store',             ['as' => '.food.eating-time.store',            'middleware' => ['ability:super-admin|account,eating-time-add'],               'uses' => 'EatingTimeController@store']);
    Route::get('food/eating-time/{id}/edit',          ['as' => '.food.eating-time.edit',             'middleware' => ['ability:super-admin|account,eating-time-edit'],              'uses' => 'EatingTimeController@edit']);
    Route::post('food/eating-time/{id}/update',       ['as' => '.food.eating-time.update',           'middleware' => ['ability:super-admin|account,eating-time-edit'],              'uses' => 'EatingTimeController@update']);
    Route::get('food/eating-time/{id}/delete',        ['as' => '.food.eating-time.delete',           'middleware' => ['ability:super-admin|account,eating-time-delete'],            'uses' => 'EatingTimeController@delete']);
    Route::get('food/eating-time/{id}/active',        ['as' => '.food.eating-time.active',           'middleware' => ['ability:super-admin|account,eating-time-active'],            'uses' => 'EatingTimeController@Active']);
    Route::get('food/eating-time/{id}/in-active',     ['as' => '.food.eating-time.in-active',        'middleware' => ['ability:super-admin|account,eating-time-in-acctive'],        'uses' => 'EatingTimeController@inActive']);
    Route::post('food/eating-time/bulk-action',       ['as' => '.food.eating-time.bulk-action',      'middleware' => ['ability:super-admin|account,eating-time-bulk-action'],       'uses' => 'EatingTimeController@bulkAction']);

    
    /*hostel leave*/
    Route::get('Leave',                       ['as' => '.Leave',                    'middleware' => ['ability:super-admin|account,hostel-leave-index'],                  'uses' => 'LeaveController@index']);
    Route::get('Leave/add',                       ['as' => '.Leave.add',                    'middleware' => ['ability:super-admin|account,hostel-leave-index'],                  'uses' => 'LeaveController@add']);
    Route::post('Leave/store',                ['as' => '.Leave.store',              'middleware' => ['ability:super-admin|account,hostel-leave-index'],                    'uses' => 'LeaveController@store']);
    Route::get('Leave/{id}/edit',             ['as' => '.Leave.edit',               'middleware' => ['ability:super-admin|account,hostel-leave-edit'],                   'uses' => 'LeaveController@edit']);
    Route::post('Leave/{id}/edit',          ['as' => '.Leave.edit',             'middleware' => ['ability:super-admin|account,hostel-leave-edit'],                   'uses' => 'LeaveController@edit']);
    Route::get('Leave/{id}/delete',           ['as' => '.Leave.delete',             'middleware' => ['ability:super-admin|account,hostel-leave-delete'],                 'uses' => 'LeaveController@delete']);
    /*hostel leave*/
});

/*Transport Grouping */
Route::group(['prefix' => 'transport/',                                 'as' => 'transport',                                    'namespace' => 'Transport\\'], function () {

    /*TRANSPOST ROUTE Types Routes*/
    Route::get('route',                         ['as' => '.route',                      'middleware' => ['ability:super-admin|account,transport-route-index'],              'uses' => 'RouteController@index']);
    Route::post('route/store',                  ['as' => '.route.store',                'middleware' => ['ability:super-admin|account,transport-route-add'],                'uses' => 'RouteController@store']);
    Route::get('route/{id}/edit',               ['as' => '.route.edit',                 'middleware' => ['ability:super-admin|account,transport-route-edit'],               'uses' => 'RouteController@edit']);
    Route::post('route/{id}/update',            ['as' => '.route.update',               'middleware' => ['ability:super-admin|account,transport-route-edit'],               'uses' => 'RouteController@update']);
    Route::get('route/{id}/delete',             ['as' => '.route.delete',               'middleware' => ['ability:super-admin|account,transport-route-delete'],             'uses' => 'RouteController@delete']);
    Route::get('route/{id}/active',             ['as' => '.route.active',               'middleware' => ['ability:super-admin|account,transport-route-active'],             'uses' => 'RouteController@Active']);
    Route::get('route/{id}/in-active',          ['as' => '.route.in-active',            'middleware' => ['ability:super-admin|account,transport-route-in-active'],          'uses' => 'RouteController@inActive']);
    Route::post('route/bulk-action',            ['as' => '.route.bulk-action',          'middleware' => ['ability:super-admin|account,transport-route-bulk-action'],        'uses' => 'RouteController@bulkAction']);
    Route::post('route/vehicle-html',           ['as' => '.route.vehicle-html',                                                                                     'uses' => 'RouteController@vehicleHtmlRow']);
    Route::get('vehicle-autocomplete',          ['as' => '.vehicle-autocomplete',                                                                                   'uses' => 'RouteController@vehicleAutocomplete']);
    /* Stoppage routes*/
    Route::get('stoppage',                  ['as'=>'.stoppage',              'middleware'=>'auth',['ability:super-admin|account','stoppage-index'],       'uses'=>'StoppageController@index']);
    Route::post('stoppage/store',                  ['as'=>'.stoppage.store',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'StoppageController@store']);
    Route::get('stoppage/edit/{id}',                  ['as'=>'.stoppage.edit',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'StoppageController@edit']);
    Route::post('stoppage/edit/{id}',                  ['as'=>'.stoppage.edit',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'StoppageController@edit']);
    Route::get('stoppage/status/{id}/{status}',                  ['as'=>'.stoppage.status',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'StoppageController@changeStatus']);
    Route::get('stoppage/delete/{id}',                  ['as'=>'.stoppage.delete',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'StoppageController@delete']);
    /*Vehical Types Routes*/
    Route::get('vehicle',                       ['as' => '.vehicle',                    'middleware' => ['ability:super-admin|account,vehicle-index'],                  'uses' => 'VehicleController@index']);
    Route::post('vehicle/store',                ['as' => '.vehicle.store',              'middleware' => ['ability:super-admin|account,vehicle-add'],                    'uses' => 'VehicleController@store']);
    Route::get('vehicle/{id}/edit',             ['as' => '.vehicle.edit',               'middleware' => ['ability:super-admin|account,vehicle-edit'],                   'uses' => 'VehicleController@edit']);
    Route::post('vehicle/{id}/update',          ['as' => '.vehicle.update',             'middleware' => ['ability:super-admin|account,vehicle-edit'],                   'uses' => 'VehicleController@update']);
    Route::get('vehicle/{id}/delete',           ['as' => '.vehicle.delete',             'middleware' => ['ability:super-admin|account,vehicle-delete'],                 'uses' => 'VehicleController@delete']);
    Route::get('vehicle/{id}/active',           ['as' => '.vehicle.active',             'middleware' => ['ability:super-admin|account,vehicle-active'],                 'uses' => 'VehicleController@Active']);
    Route::get('vehicle/{id}/in-active',        ['as' => '.vehicle.in-active',          'middleware' => ['ability:super-admin|account,vehicle-in-active'],              'uses' => 'VehicleController@inActive']);
    Route::post('vehicle/bulk-action',          ['as' => '.vehicle.bulk-action',        'middleware' => ['ability:super-admin|account,vehicle-bulk-action'],            'uses' => 'VehicleController@bulkAction']);
    Route::post('vehicle/staff-html',           ['as' => '.vehicle.staff-html',                                                                                 'uses' => 'VehicleController@staffHtmlRow']);
    Route::get('staff-autocomplete',            ['as' => '.staff-autocomplete',                                                                                 'uses' => 'VehicleController@staffAutocomplete']);

    /*Travellers Routes*/
    Route::get('user',                    ['as' => '.user',                     'middleware' => ['ability:super-admin|account,transport-user-index'],                'uses' => 'TransportUserController@index']);
    Route::get('user/add',                ['as' => '.user.add',                 'middleware' => ['ability:super-admin|account,transport-user-add'],                  'uses' => 'TransportUserController@add']);
    Route::post('user/store',             ['as' => '.user.store',               'middleware' => ['ability:super-admin|account,transport-user-add'],                  'uses' => 'TransportUserController@store']);
    Route::get('user/{id}/edit',          ['as' => '.user.edit',                'middleware' => ['ability:super-admin|account,transport-user-edit'],                 'uses' => 'TransportUserController@edit']);
    Route::post('user/{id}/update',       ['as' => '.user.update',              'middleware' => ['ability:super-admin|account,transport-user-edit'],                 'uses' => 'TransportUserController@update']);
    Route::get('user/{id}/delete',        ['as' => '.user.delete',              'middleware' => ['ability:super-admin|account,transport-user-delete'],               'uses' => 'TransportUserController@delete']);
    Route::post('user/bulk-action',       ['as' => '.user.bulk-action',         'middleware' => ['ability:super-admin|account,transport-user-bulk-action'],          'uses' => 'TransportUserController@bulkAction']);
    Route::post('user/renew',             ['as' => '.user.renew',               'middleware' => ['ability:super-admin|account,transport-user-renew'],                 'uses' => 'TransportUserController@renew']);
    Route::get('user/{id}/leave',         ['as' => '.user.leave',               'middleware' => ['ability:super-admin|account,transport-user-leave'],                 'uses' => 'TransportUserController@leave']);
    Route::post('user/shift',             ['as' => '.user.shift',               'middleware' => ['ability:super-admin|account,transport-user-shift'],                 'uses' => 'TransportUserController@shift']);
    Route::get('user/history',            ['as' => '.user.history',             'middleware' => ['ability:super-admin|account,transport-user-history'],               'uses' => 'TransportUserController@history']);
    Route::post('find-vehicles',            ['as' => '.find-vehicles',                                                                            'uses' => 'TransportUserController@findVehicles']);
    Route::post('loadRent',            ['as' => '.loadRent',                                                                            'uses' => 'TransportUserController@loadRent']);

    //Report Routes
      Route::get('report',             ['as' => '.report',               'middleware' => ['ability:super-admin|account,transport-collection-report'],    
         'uses' => 'ReportController@index']);
       Route::get('report/show',             ['as' => '.report.show',               'middleware' => ['ability:super-admin|account,transport-collection-report'],    
         'uses' => 'ReportController@show']);
        Route::get('report/print/{id}',             ['as' => '.report.print',               'middleware' => ['ability:super-admin|account,transport-collection-report'],    
         'uses' => 'ReportController@collectReceipt']);
         Route::get('report/due',             ['as' => '.report.due',               'middleware' => ['ability:super-admin|account,transport-due-report'],    
         'uses' => 'ReportController@dueReport']);
          Route::get('report/due/duereport',             ['as' => '.report.duereport',               'middleware' => ['ability:super-admin|account,transport-due-report'],    
         'uses' => 'ReportController@dueShow']);
    
       Route::post('report/bulk-action',       ['as' => '.report.bulk-action',         'middleware' => ['ability:super-admin|account,transport-user-bulk-action'],          'uses' => 'TransportUserController@bulkAction']);
     //Collect Fee 
     Route::get('collect',             ['as' => '.collect',               'middleware' => ['ability:super-admin|account,transport-collect-fee'],    
         'uses' => 'CollectionController@index']);
         Route::get('collect/show',             ['as' => '.collect.show',               'middleware' => ['ability:super-admin|account,transport-user-shift'],    
         'uses' => 'CollectionController@index']);
        Route::post('list',            ['as' => '.list',                                                                            'uses' => 'CollectionController@loadTravellers']);
         Route::post('fee',            ['as' => '.fee',                                                                            'uses' => 'CollectionController@showFee']); 
         Route::post('store',       ['as' => '.collect.store',         'middleware' => ['ability:super-admin|account,transport-user-bulk-action'],          'uses' => 'CollectionController@store']);
         Route::get('print/{id}',             ['as' => '.collect.print',     
         'uses' => 'ReportController@collectReceipt']);
         
         
    /*tranport maintenance*/
     Route::get('maintenance',                       ['as' => '.maintenance',                    'middleware' => ['ability:super-admin|account,vehicle-maintenance-index'],                  'uses' => 'VehicleMaintenanceController@index']);
    Route::post('maintenance/store',                ['as' => '.maintenance.store',              'middleware' => ['ability:super-admin|account,vehicle-maintenance-index'],                    'uses' => 'VehicleMaintenanceController@store']);
     Route::get('maintenance/{id}/edit',             ['as' => '.maintenance.edit',               'middleware' => ['ability:super-admin|account,vehicle-maintenance-edit'],                   'uses' => 'VehicleMaintenanceController@edit']);
    Route::post('maintenance/{id}/edit',          ['as' => '.maintenance.edit',             'middleware' => ['ability:super-admin|account,vehicle-maintenance-edit'],                   'uses' => 'VehicleMaintenanceController@edit']);
    Route::get('maintenance/{id}/delete',           ['as' => '.maintenance.delete',             'middleware' => ['ability:super-admin|account,vehicle-maintenance-delete'],                 'uses' => 'VehicleMaintenanceController@delete']);
    /*tranport maintenance*/
    
    /*Transport daily entry*/
    Route::get('DailyEntry',                       ['as' => '.DailyEntry',                    'middleware' => ['ability:super-admin|account,vehicle-dailyentry-index'],                  'uses' => 'VehicleDailyEntryController@index']);
    Route::post('DailyEntry/store',                ['as' => '.DailyEntry.store',              'middleware' => ['ability:super-admin|account,vehicle-dailyentry-index'],                    'uses' => 'VehicleDailyEntryController@store']);
    Route::get('DailyEntry/{id}/edit',             ['as' => '.DailyEntry.edit',               'middleware' => ['ability:super-admin|account,vehicle-dailyentry-edit'],                   'uses' => 'VehicleDailyEntryController@edit']);
    Route::post('DailyEntry/{id}/edit',          ['as' => '.DailyEntry.edit',             'middleware' => ['ability:super-admin|account,vehicle-dailyentry-edit'],                   'uses' => 'VehicleDailyEntryController@edit']);
    Route::get('DailyEntry/{id}/delete',           ['as' => '.DailyEntry.delete',             'middleware' => ['ability:super-admin|account,vehicle-dailyentry-delete'],                 'uses' => 'VehicleDailyEntryController@delete']);
    /*Transport daily entry*/     

});

/*Report Grouping*/
Route::group(['prefix' => 'report/',                                    'as' => 'report',                                       'namespace' => 'Report\\'], function () {

    Route::get('student',           ['as' => '.student',            'middleware' => 'auth',['ability:super-admin|account,student-report'],      'uses' => 'StudentReportController@index']);
    Route::get('staff',             ['as' => '.staff',              'middleware' =>'auth', ['ability:super-admin|account,staff-report'],        'uses' => 'StaffReportController@index']);
});

/*Info Center Grouping*/
Route::group(['prefix' => 'info/',                                      'as' => 'info.',                                        'namespace' => 'Info\\'], function () {

    /*Notice Board Routes*/
    Route::get('notice',                            ['as' => 'notice',                                  'middleware' => 'auth',['ability:super-admin|account,notice-index'],           'uses' => 'NoticeBoardController@index']);
    Route::get('notice/add',                        ['as' => 'notice.add',                              'middleware' => 'auth',['ability:super-admin|account,notice-index'],             'uses' => 'NoticeBoardController@add']);
    Route::post('notice/store',                     ['as' => 'notice.store',                            'middleware' =>'auth', ['ability:super-admin|account,notice-index'],             'uses' => 'NoticeBoardController@store']);
    Route::get('notice/{id}/edit',                  ['as' => 'notice.edit',                             'middleware' =>'auth', ['ability:super-admin|account,notice-index'],            'uses' => 'NoticeBoardController@edit']);
    Route::post('notice/{id}/update',               ['as' => 'notice.update',                           'middleware' =>'auth', ['ability:super-admin|account,notice-index'],            'uses' => 'NoticeBoardController@update']);
    Route::get('notice/{id}/delete',                ['as' => 'notice.delete',                           'middleware' =>'auth', ['ability:super-admin|account,notice-index'],          'uses' => 'NoticeBoardController@delete']);

    /*SMS Email Routes*/
    Route::get('smsemail',                          ['as' => 'smsemail',                                'middleware' => 'auth',['ability:super-admin|account,sms-email-index','auth'],                     'uses' => 'SmsEmailController@index']);
    Route::get('smsemail/{id}/delete',              ['as' => 'smsemail.delete',                         'middleware' => ['ability:super-admin|account,sms-email-delete','auth'],                    'uses' => 'SmsEmailController@delete']);
    Route::post('smsemail/bulk-action',             ['as' => 'smsemail.bulk-action',                    'middleware' =>['ability:super-admin|account,sms-email-bulk-action','auth'],               'uses' => 'SmsEmailController@bulkAction']);

    /*Group*/
    Route::get('smsemail/create',                   ['as' => 'smsemail.create', 'middleware' =>['ability:super-admin|account,sms-email-create','auth'],                   'uses' => 'SmsEmailController@create']);
    Route::post('smsemail/send', ['as' => 'smsemail.send', 'middleware' =>['ability:super-admin|account,sms-email-send','auth'],                     'uses' => 'SmsEmailController@send']);

    /*StudentGuardian*/
    Route::get('smsemail/student-guardian',         ['as' => 'smsemail.student-guardian',              'middleware' =>['ability:super-admin|account,sms-email-create','auth'],                     'uses' => 'SmsEmailController@studentGuardian']);
    Route::post('smsemail/student-guardian/send',   ['as' => 'smsemail.student-guardian.send',         'middleware' =>['ability:super-admin|account,sms-email-student-guardian-send','auth'],      'uses' => 'SmsEmailController@studentGuardianSend']);

    /*StudentGuardian*/
    Route::get('smsemail/staff',                    ['as' => 'smsemail.staff',                          'middleware' =>['ability:super-admin|account,sms-email-create','auth'],                   'uses' => 'SmsEmailController@staff']);
    Route::post('smsemail/staff/send',              ['as' => 'smsemail.staff.send',                     'middleware' =>['ability:super-admin|account,sms-email-staff-send','auth'],                'uses' => 'SmsEmailController@staffSend']);

    /*Individual*/
    Route::get('smsemail/individual',               ['as' => 'smsemail.individual',                     'middleware' =>['ability:super-admin|account,sms-email-create','auth'],                   'uses' => 'SmsEmailController@individual']);
    Route::post('smsemail/individual/send',         ['as' => 'smsemail.individual.send',                'middleware' =>['ability:super-admin|account,sms-email-individual-send','auth'],           'uses' => 'SmsEmailController@individualSend']);

    /*Reminder Alert*/
    Route::get('smsemail/{id}/fees-receipt',        ['as' => 'smsemail.fees-receipt',                   'middleware' =>['ability:super-admin|account,sms-email-fee-receipt','auth'],               'uses' => 'SmsEmailController@feeReceipt']);
    Route::post('smsemail/dueReminder',             ['as' => 'smsemail.dueReminder',                    'middleware' =>['ability:super-admin|account,sms-email-due-reminder','auth'],              'uses' => 'SmsEmailController@dueReminder']);
    Route::post('smsemail/bookReturnReminder',      ['as' => 'smsemail.bookReturnReminder',             'middleware' =>['ability:super-admin|account,sms-email-book-return-reminder','auth'],      'uses' => 'SmsEmailController@bookReturnReminder']);

});

/*Print Grouping*/
Route::group(['prefix' => 'print-out/',                                 'as' => 'print-out.',                                   'namespace' => 'PrintOut\\'], function () {
    /*Print Fees*/
    Route::get('fees/master-receipt/{id}',              ['as' => 'fees.master-receipt',                     'middleware' => ['ability:super-admin|account,fee-print-master'],                   'uses' => 'FeesPrintController@printMaster']);

    Route::get('fees/collection/{id}',                  ['as' => 'fees.collection',                         'middleware' => ['ability:super-admin|account,fee-print-collection'],               'uses' => 'FeesPrintController@printCollection']);
    Route::get('fees/today-receipt/{id}',               ['as' => 'fees.today-receipt',                      'middleware' => ['ability:super-admin|account,fee-print-today-receipt'],            'uses' => 'FeesPrintController@todayReceiptAmount']);
    Route::get('fees/today-receipt-detail/{id}',        ['as' => 'fees.today-receipt-detail',               'middleware' => ['ability:super-admin|account,fee-print-today-detail-receipt'],     'uses' => 'FeesPrintController@todayReceiptDetail']);
    Route::get('fees/student-ledger/{id}',              ['as' => 'fees.student-ledger',                     'middleware' => ['ability:super-admin|account,fee-print-student-ledger'],           'uses' => 'FeesPrintController@studentLedger']);

    Route::get('fees/student-due/{id}',                 ['as' => 'fees.student-due',                        'middleware' => ['ability:super-admin|account,fee-print-student-due'],              'uses' => 'FeesPrintController@studentDue']);
    Route::get('fees/student-due-detail/{id}',          ['as' => 'fees.student-due-detail',                 'middleware' => ['ability:super-admin|account,fee-print-student-due-detail'],       'uses' => 'FeesPrintController@studentDueDetail']);

    Route::post('fees/bulk-due-slip',                   ['as' => 'fees.bulk-due-slip',                       'middleware' => ['ability:super-admin|account,fee-print-bulk-due-slip'],           'uses' => 'FeesPrintController@bulkDueSlip']);
    Route::post('fees/bulk-due-detail-slip',            ['as' => 'fees.bulk-due-detail-slip',                'middleware' => ['ability:super-admin|account,fee-print-bulk-due-detail-slip'],    'uses' => 'FeesPrintController@bulkDueDetailSlip']);

    Route::post('exam/admit-card',                      ['as' => 'exam.admit-card',                         'middleware' => ['ability:super-admin|account,exam-print-admitcard'],                     'uses' => 'ExamPrintController@admitCard']);
    Route::post('exam/routine',                         ['as' => 'exam.routine',                            'middleware' => ['ability:super-admin|account,exam-print-routine'],                       'uses' => 'ExamPrintController@examRoutine']);
    Route::post('exam/mark-sheet/',                     ['as' => 'exam.mark-sheet',                         'middleware' => ['ability:super-admin|account,exam-print-mark-sheet'],                    'uses' => 'ExamPrintController@examMarkSheet']);
    Route::post('exam/grade-sheet/',                     ['as' => 'exam.grade-sheet',                         'middleware' => ['ability:super-admin|account,exam-print-mark-sheet'],                    'uses' => 'ExamPrintController@examGradeSheet']);

});

/*Academic Grouping */
Route::group(['prefix' => '/',                                          'as' => '',                                             'namespace' => 'Academic\\'], function () {
  /*Reference*/
  Route::get('reference',                  ['as'=>'reference',              'middleware'=>['ability:super-admin|account','reference-master-index','auth'],       'uses'=>'ReferenceController@index']);
     Route::post('reference/store',                  ['as'=>'reference.store',              'middleware'=>['ability:super-admin','reference-master-index','auth'],       'uses'=>'ReferenceController@store']);
      Route::get('reference/edit/{id}',                  ['as'=>'reference.edit',              'middleware'=>['ability:super-admin','reference-master-index','auth'],       'uses'=>'ReferenceController@edit']);

      Route::post('reference/edit/{id}',                  ['as'=>'reference.edit',              'middleware'=>['ability:super-admin','reference-master-index','auth'],       'uses'=>'ReferenceController@edit']);

       Route::get('reference/delete/{id}',                  ['as'=>'reference.delete',              'middleware'=>['ability:super-admin','reference-master-index','auth'],       'uses'=>'ReferenceController@delete']);
  /*Status ROUTES*/
    Route::get('status',                  ['as'=>'status',              'middleware'=>['ability:super-admin|account','status-master','auth'],       'uses'=>'StatusController@index']);

     Route::post('status/store',                  ['as'=>'status.store',              'middleware'=>'auth',['ability:super-admin|account','status-master','auth'],       'uses'=>'StatusController@store']);
      Route::get('status/edit/{id}',                  ['as'=>'status.edit',              'middleware'=>'auth',['ability:super-admin|account','status-master','auth'],       'uses'=>'StatusController@edit']);
      Route::post('status/edit/{id}',                  ['as'=>'status.edit',              'middleware'=>'auth',['ability:super-admin|account','status-master','auth'],       'uses'=>'StatusController@edit']);
       Route::get('status/delete/{id}',                  ['as'=>'status.delete',              'middleware'=>'auth',['ability:super-admin|account','status-master','auth'],       'uses'=>'StatusController@delete']);

  /*RELIGION ROUTES*/
    Route::get('religion',                  ['as'=>'religion',              'middleware'=>['ability:super-admin|account','religion-master-index','auth'],       'uses'=>'ReligionController@index']);
     Route::post('religion/store',                  ['as'=>'religion.store',              'middleware'=>['ability:super-admin|account','religion-master-index','auth'],       'uses'=>'ReligionController@store']);
      Route::get('religion/edit/{id}',                  ['as'=>'religion.edit',              'middleware'=>['ability:super-admin|account','religion-master-index','auth'],       'uses'=>'ReligionController@edit']);
      Route::post('religion/edit/{id}',                  ['as'=>'religion.edit',              'middleware'=>['ability:super-admin|account','religion-master-index','auth'],       'uses'=>'ReligionController@edit']);
       Route::get('religion/delete/{id}',                  ['as'=>'religion.delete',              'middleware'=>['ability:super-admin|account','religion-master-index','auth'],       'uses'=>'ReligionController@delete']);

       /*HANDICAP ROUTES*/
    Route::get('handicap',                  ['as'=>'handicap',              'middleware'=>['ability:super-admin|account','  handicap-master-index','auth'],       'uses'=>'HandicapController@index']);

     Route::post('handicap/store',                  ['as'=>'handicap.store',              'middleware'=>['ability:super-admin|account','  handicap-master-index','auth'],   'uses'=>'HandicapController@store']);

      Route::get('handicap/edit/{id}',                  ['as'=>'handicap.edit',              'middleware'=>['ability:super-admin|account',' handicap-master-index','auth'],       'uses'=>'HandicapController@edit']);
      Route::post('handicap/edit/{id}',                  ['as'=>'handicap.edit',              'middleware'=>['ability:super-admin|account','  handicap-master-index','auth'],       'uses'=>'HandicapController@edit']);
       Route::get('handicap/delete/{id}',                  ['as'=>'handicap.delete',              'middleware'=>['ability:super-admin|account','  handicap-master-index','auth'],       'uses'=>'HandicapController@delete']);

  /*Fee Structure*/
  Route::get('feeStructure',                  ['as'=>'feeStructure',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'FeeStructureController@index']);
     Route::post('feeStructure/store',                  ['as'=>'feeStructure.store',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'FeeStructureController@store']);
      Route::get('feeStructure/edit/{id}',                  ['as'=>'feeStructure.edit',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'FeeStructureController@edit']);
      Route::post('feeStructure/edit/{id}',                  ['as'=>'feeStructure.edit',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'FeeStructureController@edit']);
       Route::get('feeStructure/delete/{id}',                  ['as'=>'feeStructure.delete',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'FeeStructureController@delete']);
    /*Complain Type*/
    Route::get('complainType',                  ['as'=>'complainType',              'middleware'=>['ability:super-admin|account','complaint-type-master-index','auth'],       'uses'=>'ComplainTypeController@index']);
     Route::post('complainType/store',                  ['as'=>'complainType.store',              'middleware'=>['ability:super-admin|account','complaint-type-master-index','auth'],       'uses'=>'ComplainTypeController@store']);
      Route::get('complainType/edit/{id}',                  ['as'=>'complainType.edit',              'middleware'=>['ability:super-admin|account','complaint-type-master-index','auth'],       'uses'=>'ComplainTypeController@edit']);
      Route::post('complainType/edit/{id}',                  ['as'=>'complainType.edit',              'middleware'=>['ability:super-admin|account','complaint-type-master-index','auth'],       'uses'=>'ComplainTypeController@edit']);
       Route::get('complainType/delete/{id}',                  ['as'=>'complainType.delete',              'middleware'=>['ability:super-admin|account','complaint-type-master-index','auth'],       'uses'=>'ComplainTypeController@delete']);
     /*SOURCE*/  
     Route::get('source',                  ['as'=>'source',              'middleware'=>['ability:super-admin|account','source-master-index','auth'],       'uses'=>'SourceController@index']);
     Route::post('source/store',                  ['as'=>'source.store',              'middleware'=>['ability:super-admin|account','source-master-index','auth'],       'uses'=>'SourceController@store']);
      Route::get('source/edit/{id}',                  ['as'=>'source.edit',              'middleware'=>['ability:super-admin|account','source-master-index','auth'],       'uses'=>'SourceController@edit']);
      Route::post('source/edit/{id}',                  ['as'=>'source.edit',              'middleware'=>['ability:super-admin|account','source-master-index','auth'],       'uses'=>'SourceController@edit']);
       Route::get('source/delete/{id}',                  ['as'=>'source.delete',              'middleware'=>['ability:super-admin|account','source-master-index','auth'],       'uses'=>'SourceController@delete']);

    /*course-type Routes*/
    Route::get('courseType',                  ['as'=>'courseType',              'middleware'=>['ability:super-admin|account','course-type-master-index','auth'],       'uses'=>'CourseTypeController@index']);
   Route::post('courseType/store',                  ['as'=>'courseType.store',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'CourseTypeController@store']);
    Route::get('courseType/edit/{id}',                  ['as'=>'courseType.edit',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'CourseTypeController@edit']);
    Route::post('courseType/update/{id}',                  ['as'=>'courseType.update',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'CourseTypeController@edit']);
     Route::get('courseType/delete/{id}',                  ['as'=>'courseType.edit',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'CourseTypeController@delete']);
     /*Course-Batch Routes*/
    Route::get('courseBatch',                  ['as'=>'courseBatch',              'middleware'=>['ability:super-admin|account','batch-master-index','auth'],       'uses'=>'CourseBatchController@index']);
    Route::post('loadCourseByType',                  ['as'=>'loadCourseByType',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'CourseBatchController@loadCourseByType']);    
   Route::post('courseBatch/store',                  ['as'=>'courseBatch.store',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'CourseBatchController@store']);
    Route::get('courseBatch/edit/{id}',                  ['as'=>'courseBatch.edit',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'CourseBatchController@edit']);
    Route::post('courseBatch/update/{id}',                  ['as'=>'courseBatch.update',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'CourseBatchController@edit']);
     Route::get('courseBatch/delete/{id}',                  ['as'=>'courseBatch.edit',              'middleware'=>'auth',['ability:super-admin','account'],       'uses'=>'CourseBatchController@delete']);
    /*faculty Routes*/
    Route::get('faculty',                    ['as' => 'faculty',                  'middleware' => ['ability:super-admin|account,faculty-master-index','auth'],            'uses' => 'FacultyController@index']);
    Route::post('faculty/store',             ['as' => 'faculty.store',            'middleware' => ['ability:super-admin|account,faculty-add'],              'uses' => 'FacultyController@store']);
    Route::get('faculty/{id}/edit',          ['as' => 'faculty.edit',             'middleware' => ['ability:super-admin|account,faculty-edit'],             'uses' => 'FacultyController@edit']);
    Route::post('faculty/{id}/update',       ['as' => 'faculty.update',           'middleware' => ['ability:super-admin|account,faculty-edit'],             'uses' => 'FacultyController@update']);
    Route::get('faculty/{id}/delete',        ['as' => 'faculty.delete',           'middleware' => ['ability:super-admin|account,faculty-delete'],           'uses' => 'FacultyController@delete']);
    Route::get('faculty/{id}/active',        ['as' => 'faculty.active',           'middleware' => ['ability:super-admin|account,faculty-active'],           'uses' => 'FacultyController@Active']);
    Route::get('faculty/{id}/in-active',     ['as' => 'faculty.in-active',        'middleware' => ['ability:super-admin|account,faculty-in-active'],        'uses' => 'FacultyController@inActive']);
    Route::post('faculty/bulk-action',       ['as' => 'faculty.bulk-action',      'middleware' => ['ability:super-admin|account,faculty-bulk-action'],      'uses' => 'FacultyController@bulkAction']);

    /*semester Routes*/
    Route::get('semester',                    ['as' => 'semester',                  'middleware' => ['ability:super-admin|account,section-master-index','auth'],             'uses' => 'SemesterController@index']);
    Route::post('semester/store',             ['as' => 'semester.store',            'middleware' => ['ability:super-admin|account,semester-add'],               'uses' => 'SemesterController@store']);
    Route::get('semester/{id}/edit',          ['as' => 'semester.edit',             'middleware' => ['ability:super-admin|account,semester-edit'],              'uses' => 'SemesterController@edit']);
    Route::post('semester/{id}/update',       ['as' => 'semester.update',           'middleware' => ['ability:super-admin|account,semester-edit'],              'uses' => 'SemesterController@update']);
    Route::get('semester/{id}/delete',        ['as' => 'semester.delete',           'middleware' => ['ability:super-admin|account,semester-delete'],            'uses' => 'SemesterController@delete']);
    Route::get('semester/{id}/active',        ['as' => 'semester.active',           'middleware' => ['ability:super-admin|account,semester-active'],            'uses' => 'SemesterController@Active']);
    Route::get('semester/{id}/in-active',     ['as' => 'semester.in-active',        'middleware' => ['ability:super-admin|account,semester-in-active'],         'uses' => 'SemesterController@inActive']);
    Route::post('semester/bulk-action',       ['as' => 'semester.bulk-action',      'middleware' => ['ability:super-admin|account,semester-bulk-action'],       'uses' => 'SemesterController@bulkAction']);
    Route::post('semester/subject-html',        ['as' => 'semester.subject-html',                                                                       'uses' => 'SemesterController@subjectHtmlRow']);

    /*Grading Type & Scale*/
    Route::get('grading',                    ['as' => 'grading',                  'middleware' => ['ability:super-admin|account,grading-master-index','auth'],                'uses' => 'GradingController@index']);
    Route::post('grading/store',             ['as' => 'grading.store',            'middleware' => ['ability:super-admin|account,grading-master-index','auth'],                  'uses' => 'GradingController@store']);
    Route::get('grading/{id}/edit',          ['as' => 'grading.edit',             'middleware' => ['ability:super-admin|account,grading-master-index','auth'],                 'uses' => 'GradingController@edit']);
    Route::post('grading/{id}/update',       ['as' => 'grading.update',           'middleware' => ['ability:super-admin|account,grading-master-index','auth'],                 'uses' => 'GradingController@update']);
    Route::get('grading/{id}/delete',        ['as' => 'grading.delete',           'middleware' => ['ability:super-admin|account,grading-delete'],               'uses' => 'GradingController@delete']);
    Route::get('grading/{id}/active',        ['as' => 'grading.active',           'middleware' => ['ability:super-admin|account,grading-active'],               'uses' => 'GradingController@Active']);
    Route::get('grading/{id}/in-active',     ['as' => 'grading.in-active',        'middleware' => ['ability:super-admin|account,grading-in-active'],            'uses' => 'GradingController@inActive']);
    Route::post('grading/bulk-action',       ['as' => 'grading.bulk-action',      'middleware' => ['ability:super-admin|account,grading-bulk-action'],          'uses' => 'GradingController@bulkAction']);
    Route::post('grading/grade-html',        ['as' => 'grading.grade-html',                                                                                 'uses' => 'GradingController@gradeHtmlRow']);

    /*subject master code*/
    Route::get('addsubject',                    ['as' => 'addsubject',                  'middleware' => ['ability:super-admin|account,subject-master-index','auth'],             'uses' => 'SubjectMasterController@index']);

    Route::post('addsubject/store',             ['as' => 'addsubject.store',            'middleware' => ['ability:super-admin|account,subject-master-index'],               'uses' => 'SubjectMasterController@store']);

    Route::get('addsubject/{id}/edit',          ['as' => 'addsubject.edit',             'middleware' => ['ability:super-admin|account,subject-master-index'],              'uses' => 'SubjectMasterController@edit']);

    Route::post('addsubject/{id}/edit',       ['as' => 'addsubject.edit',           'middleware' => ['ability:super-admin|account,subject-master-index'],              'uses' => 'SubjectMasterController@edit']);

    Route::get('addsubject/{id}/delete',        ['as' => 'addsubject.delete',           'middleware' => ['ability:super-admin|account,subject-master-index'],            'uses' => 'SubjectMasterController@delete']);
    /*subject master code*/
    /*teacher/cordinator master*/
    Route::get('add_teacher_cordinator',                    ['as' => 'add_teacher_cordinator',                  'middleware' => ['ability:super-admin|account,teacher-co-ordinator-master-index'],             'uses' => 'TeacherCordinatorController@index']);
    Route::post('add_teacher_cordinator/store',             ['as' => 'add_teacher_cordinator.store',            'middleware' => ['ability:super-admin|account,teacher-co-ordinator-master-index'],               'uses' => 'TeacherCordinatorController@store']);
    Route::get('add_teacher_cordinator/{id}/edit',          ['as' => 'add_teacher_cordinator.edit',             'middleware' => ['ability:super-admin|account,teacher-co-ordinator-master-index'],              'uses' => 'TeacherCordinatorController@edit']);
    Route::post('add_teacher_cordinator/{id}/edit',       ['as' => 'add_teacher_cordinator.edit',           'middleware' => ['ability:super-admin|account,teacher-co-ordinator-master-index'],              'uses' => 'TeacherCordinatorController@edit']);
    Route::get('add_teacher_cordinator/{id}/delete',        ['as' => 'add_teacher_cordinator.delete',           'middleware' => ['ability:super-admin|account,teacher-co-ordinator-master-index'],            'uses' => 'TeacherCordinatorController@delete']);
    /*teacher/cordinator master*/

    /*Subject*/
    Route::get('subject',                    ['as' => 'subject',                  'middleware' => ['ability:super-admin|account,grading-course-subject-master-index','auth'],                'uses' => 'SubjectController@index']);
    Route::post('subject/store',             ['as' => 'subject.store',            'middleware' => ['ability:super-admin|account,subject-add'],                  'uses' => 'SubjectController@store']);
    Route::get('subject/{id}/edit',          ['as' => 'subject.edit',             'middleware' => ['ability:super-admin|account,subject-edit'],                 'uses' => 'SubjectController@edit']);
    Route::post('subject/{id}/update',       ['as' => 'subject.update',           'middleware' => ['ability:super-admin|account,subject-edit'],                 'uses' => 'SubjectController@update']);
    Route::get('subject/{id}/delete',        ['as' => 'subject.delete',           'middleware' => ['ability:super-admin|account,subject-delete'],               'uses' => 'SubjectController@delete']);
    Route::get('subject/{id}/active',        ['as' => 'subject.active',           'middleware' => ['ability:super-admin|account,subject-active'],               'uses' => 'SubjectController@Active']);
    Route::get('subject/{id}/in-active',     ['as' => 'subject.in-active',        'middleware' => ['ability:super-admin|account,subject-in-active'],            'uses' => 'SubjectController@inActive']);
    Route::post('subject/bulk-action',       ['as' => 'subject.bulk-action',      'middleware' => ['ability:super-admin|account,subject-bulk-action'],          'uses' => 'SubjectController@bulkAction']);
    Route::get('subject-name-autocomplete',  ['as' => 'subject-name-autocomplete',                                                                      'uses' => 'SubjectController@subjectNameAutocomplete']);

    /*Student Status Routes*/
    Route::get('student-status',                    ['as' => 'student-status',                  'middleware' => ['ability:super-admin|account,student-status-index'],               'uses' => 'StudentStatusController@index']);
    Route::post('student-status/store',             ['as' => 'student-status.store',            'middleware' => ['ability:super-admin|account,student-status-add'],                 'uses' => 'StudentStatusController@store']);
    Route::get('student-status/{id}/edit',          ['as' => 'student-status.edit',             'middleware' => ['ability:super-admin|account,student-status-edit'],                'uses' => 'StudentStatusController@edit']);
    Route::post('student-status/{id}/update',       ['as' => 'student-status.update',           'middleware' => ['ability:super-admin|account,student-status-edit'],                'uses' => 'StudentStatusController@update']);
    Route::get('student-status/{id}/delete',        ['as' => 'student-status.delete',           'middleware' => ['ability:super-admin|account,student-status-delete'],              'uses' => 'StudentStatusController@delete']);
    Route::get('student-status/{id}/active',        ['as' => 'student-status.active',           'middleware' => ['ability:super-admin|account,student-status-active'],              'uses' => 'StudentStatusController@Active']);
    Route::get('student-status/{id}/in-active',     ['as' => 'student-status.in-active',        'middleware' => ['ability:super-admin|account,student-status-in-active'],           'uses' => 'StudentStatusController@inActive']);
    Route::post('student-status/bulk-action',       ['as' => 'student-status.bulk-action',      'middleware' => ['ability:super-admin|account,student-status-bulk-action'],         'uses' => 'StudentStatusController@bulkAction']);

    /*Book Status Routes*/
    Route::get('attendance-status',                    ['as' => 'attendance-status',                  'middleware' => ['ability:super-admin|account,attendance-status-index'],            'uses' => 'AttendanceStatusController@index']);
    Route::post('attendance-status/store',             ['as' => 'attendance-status.store',            'middleware' => ['ability:super-admin|account,attendance-status-add'],              'uses' => 'AttendanceStatusController@store']);
    Route::get('attendance-status/{id}/edit',          ['as' => 'attendance-status.edit',             'middleware' => ['ability:super-admin|account,attendance-status-edit'],             'uses' => 'AttendanceStatusController@edit']);
    Route::post('attendance-status/{id}/update',       ['as' => 'attendance-status.update',           'middleware' => ['ability:super-admin|account,attendance-status-edit'],             'uses' => 'AttendanceStatusController@update']);
    Route::get('attendance-status/{id}/delete',        ['as' => 'attendance-status.delete',           'middleware' => ['ability:super-admin|account,attendance-status-delete'],           'uses' => 'AttendanceStatusController@delete']);
    Route::get('attendance-status/{id}/active',        ['as' => 'attendance-status.active',           'middleware' => ['ability:super-admin|account,attendance-status-active'],           'uses' => 'AttendanceStatusController@Active']);
    Route::get('attendance-status/{id}/in-active',     ['as' => 'attendance-status.in-active',        'middleware' => ['ability:super-admin|account,attendance-status-in-active'],        'uses' => 'AttendanceStatusController@inActive']);
    Route::post('attendance-status/bulk-action',       ['as' => 'attendance-status.bulk-action',      'middleware' => ['ability:super-admin|account,attendance-status-bulk-action'],      'uses' => 'AttendanceStatusController@bulkAction']);

    /*Book Status Routes*/
    Route::get('book-status',                    ['as' => 'book-status',                  'middleware' => ['ability:super-admin|account,book-status-index'],            'uses' => 'BookStatusController@index']);
    Route::post('book-status/store',             ['as' => 'book-status.store',            'middleware' => ['ability:super-admin|account,book-status-add'],              'uses' => 'BookStatusController@store']);
    Route::get('book-status/{id}/edit',          ['as' => 'book-status.edit',             'middleware' => ['ability:super-admin|account,book-status-edit'],             'uses' => 'BookStatusController@edit']);
    Route::post('book-status/{id}/update',       ['as' => 'book-status.update',           'middleware' => ['ability:super-admin|account,book-status-edit'],             'uses' => 'BookStatusController@update']);
    Route::get('book-status/{id}/delete',        ['as' => 'book-status.delete',           'middleware' => ['ability:super-admin|account,book-status-delete'],           'uses' => 'BookStatusController@delete']);
    Route::get('book-status/{id}/active',        ['as' => 'book-status.active',           'middleware' => ['ability:super-admin|account,book-status-active'],           'uses' => 'BookStatusController@Active']);
    Route::get('book-status/{id}/in-active',     ['as' => 'book-status.in-active',        'middleware' => ['ability:super-admin|account,book-status-in-active'],        'uses' => 'BookStatusController@inActive']);
    Route::post('book-status/bulk-action',       ['as' => 'book-status.bulk-action',      'middleware' => ['ability:super-admin|account,book-status-bulk-action'],      'uses' => 'BookStatusController@bulkAction']);

    /*Hostel Room Beds Status Routes*/
    Route::get('bed-status',                    ['as' => 'bed-status',                  'middleware' => ['ability:super-admin|account,bed-status-index'],               'uses' => 'BedStatusController@index']);
    Route::post('bed-status/store',             ['as' => 'bed-status.store',            'middleware' => ['ability:super-admin|account,bed-status-add'],                 'uses' => 'BedStatusController@store']);
    Route::get('bed-status/{id}/edit',          ['as' => 'bed-status.edit',             'middleware' => ['ability:super-admin|account,bed-status-edit'],                'uses' => 'BedStatusController@edit']);
    Route::post('bed-status/{id}/update',       ['as' => 'bed-status.update',           'middleware' => ['ability:super-admin|account,bed-status-edit'],                'uses' => 'BedStatusController@update']);
    Route::get('bed-status/{id}/delete',        ['as' => 'bed-status.delete',           'middleware' => ['ability:super-admin|account,bed-status-delete'],              'uses' => 'BedStatusController@delete']);
    Route::get('bed-status/{id}/active',        ['as' => 'bed-status.active',           'middleware' => ['ability:super-admin|account,bed-status-active'],              'uses' => 'BedStatusController@Active']);
    Route::get('bed-status/{id}/in-active',     ['as' => 'bed-status.in-active',        'middleware' => ['ability:super-admin|account,bed-status-in-active'],           'uses' => 'BedStatusController@inActive']);
    Route::post('bed-status/bulk-action',       ['as' => 'bed-status.bulk-action',      'middleware' => ['ability:super-admin|account,bed-status-bulk-action'],          'uses' => 'BedStatusController@bulkAction']);

    /*Year Routes*/
    Route::get('year',                    ['as' => 'year',                  'middleware' => ['ability:super-admin|account,year-master-index','auth'],            'uses' => 'YearsController@index']);
    Route::post('year/store',             ['as' => 'year.store',            'middleware' => ['ability:super-admin|account,year-add'],              'uses' => 'YearsController@store']);
    Route::get('year/{id}/edit',          ['as' => 'year.edit',             'middleware' => ['ability:super-admin|account,year-edit'],             'uses' => 'YearsController@edit']);
    Route::post('year/{id}/update',       ['as' => 'year.update',           'middleware' => ['ability:super-admin|account,year-edit'],             'uses' => 'YearsController@update']);
    Route::get('year/{id}/delete',        ['as' => 'year.delete',           'middleware' => ['ability:super-admin|account,year-delete'],           'uses' => 'YearsController@delete']);
    Route::get('year/{id}/active',        ['as' => 'year.active',           'middleware' => ['ability:super-admin|account,year-active'],           'uses' => 'YearsController@Active']);
    Route::get('year/{id}/in-active',     ['as' => 'year.in-active',        'middleware' => ['ability:super-admin|account,year-in-active'],        'uses' => 'YearsController@inActive']);
    Route::post('year/bulk-action',       ['as' => 'year.bulk-action',      'middleware' => ['ability:super-admin|account,year-bulk-action'],      'uses' => 'YearsController@bulkAction']);
    Route::get('year/{id}/active-status', ['as' => 'year.active-status',    'middleware' => ['ability:super-admin|account,year-active-status'],    'uses' => 'YearsController@activeStatus']);

    /*Months Routes*/
    Route::get('month',                    ['as' => 'month',                  'middleware' => ['ability:super-admin|account,month-master-index','auth'],          'uses' => 'MonthsController@index']);
    Route::post('month/store',             ['as' => 'month.store',            'middleware' => ['ability:super-admin|account,month-add'],            'uses' => 'MonthsController@store']);
    Route::get('month/{id}/edit',          ['as' => 'month.edit',             'middleware' => ['ability:super-admin|account,month-edit'],           'uses' => 'MonthsController@edit']);
    Route::post('month/{id}/update',       ['as' => 'month.update',           'middleware' => ['ability:super-admin|account,month-edit'],           'uses' => 'MonthsController@update']);
    Route::get('month/{id}/delete',        ['as' => 'month.delete',           'middleware' => ['ability:super-admin|account,month-delete'],         'uses' => 'MonthsController@delete']);
    Route::get('month/{id}/active',        ['as' => 'month.active',           'middleware' => ['ability:super-admin|account,month-active'],         'uses' => 'MonthsController@Active']);
    Route::get('month/{id}/in-active',     ['as' => 'month.in-active',        'middleware' => ['ability:super-admin|account,month-in-active'],      'uses' => 'MonthsController@inActive']);
    Route::post('month/bulk-action',       ['as' => 'month.bulk-action',      'middleware' => ['ability:super-admin|account,month-bulk-action'],    'uses' => 'MonthsController@bulkAction']);

    /*Day Routes*/
    Route::get('day',                    ['as' => 'day',                  'middleware' => ['ability:super-admin|account,day-master-index','auth'],            'uses' => 'DayController@index']);
    Route::post('day/store',             ['as' => 'day.store',            'middleware' => ['ability:super-admin|account,day-add'],              'uses' => 'DayController@store']);
    Route::get('day/{id}/edit',          ['as' => 'day.edit',             'middleware' => ['ability:super-admin|account,day-edit'],             'uses' => 'DayController@edit']);
    Route::post('day/{id}/update',       ['as' => 'day.update',           'middleware' => ['ability:super-admin|account,day-edit'],             'uses' => 'DayController@update']);
    Route::get('day/{id}/delete',        ['as' => 'day.delete',           'middleware' => ['ability:super-admin|account,day-delete'],           'uses' => 'DayController@delete']);
    Route::get('day/{id}/active',        ['as' => 'day.active',           'middleware' => ['ability:super-admin|account,day-active'],           'uses' => 'DayController@Active']);
    Route::get('day/{id}/in-active',     ['as' => 'day.in-active',        'middleware' => ['ability:super-admin|account,day-in-active'],        'uses' => 'DayController@inActive']);
    Route::post('day/bulk-action',       ['as' => 'day.bulk-action',      'middleware' => ['ability:super-admin|account,day-bulk-action'],      'uses' => 'DayController@bulkAction']);
    
    
    
    /*career status mster*/
    Route::get('career-status',                    ['as' => 'career-status',                  'middleware' => ['ability:super-admin|account,career-status-index'],               'uses' => 'CareerStatusController@index']);
    Route::post('career-status/store',             ['as' => 'career-status.store',            'middleware' => ['ability:super-admin|account,career-status-add'],                 'uses' => 'CareerStatusController@store']);
    Route::get('career-status/{id}/edit',          ['as' => 'career-status.edit',             'middleware' => ['ability:super-admin|account,career-status-edit'],                'uses' => 'CareerStatusController@edit']);
    Route::post('career-status/{id}/edit',       ['as' => 'career-status.edit',           'middleware' => ['ability:super-admin|account,career-status-edit'],                'uses' => 'CareerStatusController@edit']);
    Route::get('career-status/{id}/delete',        ['as' => 'career-status.delete',           'middleware' => ['ability:super-admin|account,career-status-delete'],              'uses' => 'CareerStatusController@delete']);
});

/*Setting Grouping */
Route::group(['prefix' => 'setting/',                                   'as' => 'setting',                                      'namespace' => 'Setting\\'], function () {
    /* General Setting Routes */
    Route::get('general',                    ['as' => '.general',                  'middleware' => ['ability:super-admin|account,general-setting-index'],           'uses' => 'GeneralSettingController@index']);
    Route::get('general/add',                ['as' => '.general.add',              'middleware' => ['ability:super-admin|account,general-setting-add'],             'uses' => 'GeneralSettingController@add']);
    Route::post('general/store',             ['as' => '.general.store',            'middleware' => ['ability:super-admin|account,general-setting-add'],             'uses' => 'GeneralSettingController@store']);
    Route::get('general/{id}/edit',          ['as' => '.general.edit',             'middleware' => ['ability:super-admin|account,general-setting-edit'],            'uses' => 'GeneralSettingController@edit']);
    Route::post('general/{id}/update',       ['as' => '.general.update',           'middleware' => ['ability:super-admin|account,general-setting-edit'],            'uses' => 'GeneralSettingController@update']);

    /* Alert Setting Routes */
    Route::get('alert',                    ['as' => '.alert',                   'middleware' => ['ability:super-admin|account,alert-setting-index'],        'uses' => 'AlertSettingController@index']);
    Route::get('alert/add',                ['as' => '.alert.add',               'middleware' => ['ability:super-admin|account,alert-setting-add'],          'uses' => 'AlertSettingController@add']);
    Route::post('alert/store',             ['as' => '.alert.store',             'middleware' => ['ability:super-admin|account,alert-setting-add'],          'uses' => 'AlertSettingController@store']);
    Route::get('alert/{id}/edit',          ['as' => '.alert.edit',              'middleware' => ['ability:super-admin|account,alert-setting-edit'],         'uses' => 'AlertSettingController@edit']);
    Route::post('alert/{id}/update',       ['as' => '.alert.update',            'middleware' => ['ability:super-admin|account,alert-setting-edit'],         'uses' => 'AlertSettingController@update']);

    /* SMS Setting Routes */
    Route::get('sms',                    ['as' => '.sms',                           'middleware' => ['ability:super-admin|account,sms-setting-index'],              'uses' => 'SmsSettingController@index']);
    Route::get('sms/add',                ['as' => '.sms.add',                       'middleware' => ['ability:super-admin|account,sms-setting-add'],                'uses' => 'SmsSettingController@add']);
    Route::post('sms/store',             ['as' => '.sms.store',                     'middleware' => ['ability:super-admin|account,sms-setting-add'],                'uses' => 'SmsSettingController@store']);
    Route::get('sms/{id}/edit',          ['as' => '.sms.edit',                      'middleware' => ['ability:super-admin|account,sms-setting-edit'],               'uses' => 'SmsSettingController@edit']);
    Route::post('sms/{id}/update',       ['as' => '.sms.update',                    'middleware' => ['ability:super-admin|account,sms-setting-edit'],               'uses' => 'SmsSettingController@update']);
    Route::get('sms/{id}/active',        ['as' => '.sms.active',                    'middleware' => ['ability:super-admin|account,sms-active'],                      'uses' => 'SmsSettingController@Active']);
    Route::get('sms/{id}/in-active',     ['as' => '.sms.in-active',                 'middleware' => ['ability:super-admin|account,sms-in-active'],                  'uses' => 'SmsSettingController@inActive']);

    /* Email Setting Routes */
    Route::get('email',                    ['as' => '.email',                       'middleware' => ['ability:super-admin|account,email-setting-index'],                'uses' => 'EmailSettingController@index']);
    Route::get('email/add',                ['as' => '.email.add',                   'middleware' => ['ability:super-admin|account,email-setting-add'],                  'uses' => 'EmailSettingController@add']);
    Route::post('email/store',             ['as' => '.email.store',                 'middleware' => ['ability:super-admin|account,email-setting-add'],                  'uses' => 'EmailSettingController@store']);
    Route::get('email/{id}/edit',          ['as' => '.email.edit',                  'middleware' => ['ability:super-admin|account,email-setting-edit'],                 'uses' => 'EmailSettingController@edit']);
    Route::post('email/{id}/update',       ['as' => '.email.update',                'middleware' => ['ability:super-admin|account,email-setting-edit'],                 'uses' => 'EmailSettingController@update']);
    Route::post('email/change-status',     ['as' => '.email.change-status',         'middleware' => ['ability:super-admin|account,email-setting-status-change'],        'uses' => 'EmailSettingController@statusChange']);

    /* Email Setting Routes */
    Route::get('payment-gateway',                    ['as' => '.payment-gateway',                       'middleware' => ['ability:super-admin|account,payment-gateway-setting-index'],                'uses' => 'PaymentSettingController@index']);
    Route::get('payment-gateway/add',                ['as' => '.payment-gateway.add',                   'middleware' => ['ability:super-admin|account,payment-gateway-setting-add'],                  'uses' => 'PaymentSettingController@add']);
    Route::post('payment-gateway/store',             ['as' => '.payment-gateway.store',                 'middleware' => ['ability:super-admin|account,payment-gateway-setting-add'],                  'uses' => 'PaymentSettingController@store']);
    Route::get('payment-gateway/{id}/edit',          ['as' => '.payment-gateway.edit',                  'middleware' => ['ability:super-admin|account,payment-gateway-setting-edit'],                 'uses' => 'PaymentSettingController@edit']);
    Route::post('payment-gateway/{id}/update',       ['as' => '.payment-gateway.update',                'middleware' => ['ability:super-admin|account,payment-gateway-setting-edit'],                 'uses' => 'PaymentSettingController@update']);
    Route::get('payment-gateway/{id}/active',        ['as' => '.payment-gateway.active',                 'middleware' => ['ability:super-admin|account,payment-gateway-active'],                       'uses' => 'PaymentSettingController@Active']);
    Route::get('payment-gateway/{id}/in-active',     ['as' => '.payment-gateway.in-active',              'middleware' => ['ability:super-admin|account,payment-gateway-in-active'],                     'uses' => 'PaymentSettingController@inActive']);
    /* Menu Settings */
    Route::get('menu',                    ['as' => '.menu',                  'middleware' => ['ability:super-admin|super-admin,menu-settings'],           'uses' => 'MenuSettingController@index']);
    Route::post('menu/store',                    ['as' => '.menu.store',                  'middleware' => ['ability:super-admin|super-admin,menu-settings'],           'uses' => 'MenuSettingController@store']);
    Route::post('menu/edit/{id}',                    ['as' => '.menu.edit',                  'middleware' => ['ability:super-admin|super-admin,menu-settings'],           'uses' => 'MenuSettingController@edit']);
    Route::get('menu/edit/{id}',                    ['as' => '.menu.edit',                  'middleware' => ['ability:super-admin|super-admin,menu-settings'],           'uses' => 'MenuSettingController@edit']);
    Route::get('menu/delete/{id}',                    ['as' => '.menu.delete',                  'middleware' => ['ability:super-admin|super-admin,menu-settings'],           'uses' => 'MenuSettingController@delete']);

});


/*Extra Features Grouping */

/*Assignment Grouping */
Route::group(['prefix' => 'assignment/',                                    'as' => 'assignment',                                       'namespace' => 'Assignment\\'], function () {

    /*Assignment Routes*/
    Route::get('',                   ['as' => '',                  'middleware' => ['ability:super-admin|account,assignment-index','auth'],                'uses' => 'AssignmentController@index']);
    Route::get('add',                ['as' => '.add',              'middleware' => ['ability:super-admin|account,assignment-add','auth'],                  'uses' => 'AssignmentController@add']);
    Route::post('store',             ['as' => '.store',            'middleware' => ['ability:super-admin|account,assignment-add','auth'],                  'uses' => 'AssignmentController@store']);
    Route::get('{id}/edit',          ['as' => '.edit',             'middleware' => ['ability:super-admin|account,assignment-edit','auth'],                 'uses' => 'AssignmentController@edit']);
    Route::post('{id}/update',       ['as' => '.update',           'middleware' => ['ability:super-admin|account,assignment-edit','auth'],                 'uses' => 'AssignmentController@update']);
    Route::get('{id}/view',          ['as' => '.view',             'middleware' => ['ability:super-admin|account,assignment-view','auth'],                 'uses' => 'AssignmentController@view']);
    Route::get('{id}/delete',        ['as' => '.delete',           'middleware' => ['ability:super-admin|account,assignment-delete','auth'],               'uses' => 'AssignmentController@delete']);
    Route::get('{id}/active',        ['as' => '.active',           'middleware' => ['ability:super-admin|account,assignment-active','auth'],               'uses' => 'AssignmentController@Active']);
    Route::get('{id}/in-active',     ['as' => '.in-active',        'middleware' => ['ability:super-admin|account,assignment-in-active','auth'],            'uses' => 'AssignmentController@inActive']);
    Route::post('bulk-action',       ['as' => '.bulk-action',      'middleware' => ['ability:super-admin|account,assignment-bulk-action','auth'],          'uses' => 'AssignmentController@bulkAction']);

    Route::post('find-semester',                    ['as' => '.find-semester',                                                              'uses' => 'AssignmentController@findSemester']);
    Route::post('mark-ledger/find-subject',         ['as' => '.find-subject',                                                               'uses' => 'AssignmentController@findSubject']);

    /*Answer Routes*/
    Route::get('answer/{id}/{answer}/view',     ['as' => '.answer.view',                'middleware' => ['ability:super-admin|account,assignment-answer-view','auth'],                 'uses' => 'AssignmentController@viewAnswer']);
    Route::post('answer/{id}/{answer}/view',     ['as' => '.answer.view',                'middleware' => ['ability:super-admin|account,assignment-answer-view','auth'],                 'uses' => 'AssignmentController@viewAnswer']);
    Route::get('answer/{id}/approve',           ['as' => '.answer.approve',             'middleware' => ['ability:super-admin|account,assignment-answer-approve','auth'],              'uses' => 'AssignmentController@approveAnswer']);
    Route::get('answer/{id}/reject',            ['as' => '.answer.reject',              'middleware' => ['ability:super-admin|account,assignment-answer-reject','auth'],               'uses' => 'AssignmentController@rejectAnswer']);
    Route::get('answer/{id}/delete',            ['as' => '.answer.delete',              'middleware' => ['ability:super-admin|account,assignment-answer-delete','auth'],               'uses' => 'AssignmentController@deleteAnswer']);

    Route::post('answer/bulk-action',           ['as' => '.answer.bulk-action',      'middleware' => ['ability:super-admin|account,assignment-answer-bulk-action','auth'],             'uses' => 'AssignmentController@bulkActionAnswer']);
});


/*Download Grouping */
Route::group(['prefix' => 'download/',                                    'as' => 'download',                                       'namespace' => 'Download\\'], function () {
    /*Download Routes*/
    Route::get('',                   ['as' => '',                  'middleware' => ['ability:super-admin|account,download-index','auth'],                'uses' => 'DownloadController@index']);
    Route::post('store',             ['as' => '.store',            'middleware' => ['ability:super-admin|account,download-add','auth'],                  'uses' => 'DownloadController@store']);
    Route::get('{id}/edit',          ['as' => '.edit',             'middleware' => ['ability:super-admin|account,download-edit','auth'],                 'uses' => 'DownloadController@edit']);
    Route::post('{id}/update',       ['as' => '.update',           'middleware' => ['ability:super-admin|account,download-edit','auth'],                 'uses' => 'DownloadController@update']);
    Route::get('{id}/delete',        ['as' => '.delete',           'middleware' => ['ability:super-admin|account,download-delete','auth'],               'uses' => 'DownloadController@delete']);
    Route::get('{id}/active',        ['as' => '.active',           'middleware' => ['ability:super-admin|account,download-active','auth'],               'uses' => 'DownloadController@Active']);
    Route::get('{id}/in-active',     ['as' => '.in-active',        'middleware' => ['ability:super-admin|account,download-in-active','auth'],            'uses' => 'DownloadController@inActive']);
    Route::post('bulk-action',       ['as' => '.bulk-action',      'middleware' => ['ability:super-admin|account,download-bulk-action','auth'],          'uses' => 'DownloadController@bulkAction']);
});


//-----------------------------Aj


Route::get('/studentfeeReceipt/{receipt_no}',[
    'as' => 'feeReceipt', 'middleware'=> 'auth',
    'uses' => 'Student\StudentController@feeReceipt'
]);

Route::get('student/NoDue/{studentid}',[
    'as' => 'noDueReceipt', 'middleware'=> 'auth',
    'uses' => 'NoDue\NoDueController@nodue'
]);

//-----------------------------Grav


Route::middleware(['auth'])->group(function(){ 


Route::get('/student/api/getcourseby/{cid}', 'Academic\FacultyController@getcourseapi');

Route::get('/assignList',  'AssignFeeController@index')->name('assignList');

Route::post('/assign_fee', 'AssignFeeController@assigned')->name('assign_fee');

Route::get('/collect_fee', 'AssignFeeController@collect')->name('collect_fee');
Route::post('/collect_fee', 'AssignFeeController@collect')->name('collect_fee');
/*start  bulk collect fee*/
Route::get('/bulk_collect_fee', ['as'=>'bulk_collect_fee','middleware'=>['ability:super-admin|account,bulk-collect-fees','auth'],'uses'=>'AssignFeeController@BulkcollectFee']);

Route::post('/bulk_collect_fee', ['as'=>'bulk_collect_fee', 'middleware'=>['ability:super-admin|account,bulk-collect-fees','auth'],'uses'=>'AssignFeeController@BulkcollectFee']);
/*end  bulk collect fee*/
Route::post('/branch_select', 'AssignFeeController@branch_select')->name('branch_select');

Route::post('/student_select', 'AssignFeeController@student_select')->name('student_select');
Route::post('/getBatchByCourse', 'AssignFeeController@getBatchByCourse')->name('getBatchByCourse');
Route::post('/getStudentByBatch', 'AssignFeeController@getStudentByBatch')->name('getStudentByBatch');
Route::post('/getFeeByBatch', 'AssignFeeController@getFeeByBatch')->name('getFeeByBatch');
Route::post('/getSubject', 'Student\StudentController@getSubject')->name('getSubject');
Route::post('/load_feeStructure', 'AssignFeeController@load_feeStructure')->name('load_feeStructure');

/*Transport Info*/
Route::post('/addroute', 'Transport\RouteController@AddRoute')->name('addroute');
Route::post('/addstoppage', 'Transport\StoppageController@AddStoppage')->name('addstoppage');
/*Transport Info*/

Route::post('/student_fee', 'AssignFeeController@student_fee')->name('student_fee');
Route::post('/bulk_student_fee', 'AssignFeeController@bulk_student_fee')->name('bulk_student_fee');
Route::post('/get_std_id', 'AssignFeeController@get_std_id')->name('get_std_id');
Route::post('/student_fee_history', 'AssignFeeController@student_fee_history')->name('student_fee_history');

Route::get('/collection_List', ['as'=>'collection_List','middleware'=>['ability:super-admin|account,collection-list','auth'],'uses'=>'AssignFeeController@collection_List']);

Route::get('/discount_status', [ 'as'=>'discount_status', 'middleware'=>['ability:super-admin|account,discount-status','auth'], 'uses'=>'AssignFeeController@discount_status']);

Route::post('/change_discount_status', ['as'=>'change_discount_status','middleware'=>['ability:super-admin|account,discount-status','auth'],'uses'=>'AssignFeeController@change_discount_status'])
;

Route::get('/edit/fee_collection/{id}',       ['as' => 'edit.fee_collection',           'middleware'=>['ability:super-admin|account,fees-edit-collection','auth'],                 'uses' => 'AssignFeeController@edit_collection']);

Route::post('/edit/fee_collection/{id}',       ['as' => 'edit.fee_collection',          'middleware'=>['ability:super-admin|account,fees-edit-collection','auth'],                 'uses' => 'AssignFeeController@edit_collection']);
/*Route::get('/edit/fee_collection/{id}', 'AssignFeeController@edit_collection')->name('edit.fee_collection');*/
Route::get('/delete/fee_collection/{id}',       ['as' => 'delete.fee_collection',          'middleware'=>['ability:super-admin|account,fees-delete-collection','auth'],                 'uses' => 'AssignFeeController@delete_collection']);

Route::get('/cancel/fee_receipt/{id}',       ['as' => 'cancel.fee_receipt',           'middleware'=>['ability:super-admin|account,cancel-receipt','auth'],                 'uses' => 'AssignFeeController@cancel_receipt']);
/* Route::get('/delete/fee_collection/{id}', 'AssignFeeController@delete_collection')->name('delete.fee_collection');
// Route::post('/edit/fee_collection/{id}', 'AssignFeeController@edit_collection')->name('edit.fee_collection');
Route::get('/cancel/fee_receipt/{id}', 'AssignFeeController@cancel_receipt')->name('cancel.fee_receipt'); */


Route::get('/branch', 'BranchController@index')->name('branch');
Route::get('/branch/{id}', 'BranchController@index')->name('branch_edit');
Route::post('/branch_ops', 'BranchController@branch_ops')->name('branch_ops');

Route::get('/branch_batchwise', 'BranchController@batch_index')->name('branch_batchwise');
Route::get('/branch_batchwise/edit/{id}', 'BranchController@batch_index')->name('branch_batchwise.edit');
Route::post('/branch_batchwise/edit/{id}', 'BranchController@batch_index')->name('branch_batchwise.edit');
Route::post('/branch_batchwise', 'BranchController@batch_index')->name('branch_batchwise');

Route::get('/payment_type', 'BranchController@payment_type')->name('payment_type');
Route::get('/payment_type/{id?}', 'BranchController@payment_type');
Route::post('payment_type', 'BranchController@payment_ops')->name('payment_type');


//-----------------------------Grav
Route::get('/student/api/getcourseby/{cid}', 'Academic\FacultyController@getcourseapi');

Route::get('/assignList', 'AssignFeeController@index')->name('assignList');

Route::post('/assign_fee', 'AssignFeeController@assigned')->name('assign_fee');

 
 


Route::get('/newAssignList', [ 'as'=>'newAssignList','middleware' =>['ability:super-admin|account,fee-new-assign-list','auth'], 'uses'=>'AssignFeeController@new_index']);
Route::get('/assign_fee_list', [ 'as'=>'assign_fee_list','middleware' =>['ability:super-admin|account,fee-new-assign-list','auth'], 'uses'=>'AssignFeeController@assign_fee_list']);

Route::post('/new_assign_fee',[ 'as'=>'new_assign_fee','middleware' =>['ability:super-admin|account,new-assign-fee','auth'] ,'uses'=> 'AssignFeeController@new_assigned']);

Route::get('/assignFee/{id}/delete',[ 'as'=>'assignFee.delete','middleware' =>['ability:super-admin|account,assign-fee-delete','auth'],'uses'=> 'AssignFeeController@deleteAssignFee']);

Route::post('/loadAssignFees', 'AssignFeeController@loadAssignFees')->name('loadAssignFees');

Route::post('/edit/assignfee',[ 'as'=>'edit.assignfee','middleware' =>['ability:super-admin|account,assign-fee-edit','auth'], 'uses'=> 'AssignFeeController@editAssignFees']);

Route::get('/collect_fee', ['as'=>'collect_fee','middleware'=>['ability:super-admin|account,collect-fees','auth'],'uses'=>'AssignFeeController@collect']);

Route::post('/collect_fee', ['as'=>'collect_fee','middleware'=>['ability:super-admin|account,collect-fees','auth'],'uses'=>'AssignFeeController@collect']);

Route::post('/branch_select', 'AssignFeeController@branch_select')->name('branch_select');

Route::post('/student_select', 'AssignFeeController@student_select')->name('student_select');

Route::post('/student_fee', 'AssignFeeController@student_fee')->name('student_fee');

Route::get('/collection_List', ['as'=>'collection_List','middleware'=>['ability:super-admin|account,collection-list','auth'],'uses'=>'AssignFeeController@collection_List']);


Route::get('/cancelled_receipts', ['as'=>'cancelled_receipts','middleware'=>['ability:super-admin|account,cancel-receipt','auth'],'uses'=>'AssignFeeController@cancelled_receipts']);

Route::get('dueReport', ['as'=>'due_report', 'middleware'=>['ability:super-admin|account,due-report','auth'], 'uses'=>'AssignFeeController@dueReport']);

Route::post('dueReport', ['as'=>'due_report','middleware'=>['ability:super-admin|account,due-report','auth'], 'uses'=>'AssignFeeController@dueReport']);

Route::get('feeReport', ['as'=>'fee_report','middleware'=>['ability:super-admin|account,report-payment-type','auth'], 'uses'=>'AssignFeeController@feeReport']);

Route::post('feeReport', ['as'=>'fee_report','middleware'=>['ability:super-admin|account,report-payment-type','auth'], 'uses'=>'AssignFeeController@feeReport']);

Route::get('feeReportMonthWise', ['as'=>'fee_report_month_wise', 'middleware'=>['ability:super-admin|account,report-month-wise','auth'], 'uses'=>'AssignFeeController@feeReport']);
Route::post('feeReportMonthWise', ['as'=>'fee_report_month_wise', 'middleware'=>['ability:super-admin|account,report-month-wise','auth'], 'uses'=>'AssignFeeController@feeReport']);

Route::get('noDues', ['as'=>'noDues',  'middleware'=>['ability:super-admin|account,report-no-due-headwise-cumulative','auth'],'uses'=>'AssignFeeController@noDues']);
Route::post('noDues', ['as'=>'noDues', 'middleware'=>['ability:super-admin|account,report-no-due-headwise-cumulative','auth'], 'uses'=>'AssignFeeController@noDues']);

Route::get('noDuesMonthWise', ['as'=>'noDuesMonthWise', 'middleware'=>['ability:super-admin|account,no-due-month-wise','auth'], 'uses'=>'AssignFeeController@noDues']);
Route::post('noDuesMonthWise', ['as'=>'noDuesMonthWise','middleware'=>['ability:super-admin|account,no-due-month-wise','auth'], 'uses'=>'AssignFeeController@noDues']);

Route::get('noDues_student', ['as'=>'noDues_student','middleware'=>['ability:super-admin|account,report-student-headwise','auth'], 'uses'=>'AssignFeeController@noDues_student']);
Route::post('noDues_student', ['as'=>'noDues_student', 'middleware'=>['ability:super-admin|account,report-student-headwise','auth'], 'uses'=>'AssignFeeController@noDues_student']);

Route::get('noDues_studentMonthWise', ['as'=>'noDues_studentMonthWise','middleware'=>['ability:super-admin|account,report-student-monthwise','auth'], 'uses'=>'AssignFeeController@noDues_student']);
Route::post('noDues_studentMonthWise', ['as'=>'noDues_studentMonthWise','middleware'=>['ability:super-admin|account,report-student-monthwise','auth'], 'uses'=>'AssignFeeController@noDues_student']);

Route::get('headwiseTotalReport', ['as'=>'headwiseTotalReport','middleware'=>['ability:super-admin|account,report-head-collection-due','auth'], 'uses'=>'AssignFeeController@headwiseTotalReport']);

Route::post('headwiseTotalReport', ['as'=>'headwiseTotalReport','middleware'=>['ability:super-admin|account,report-head-collection-due','auth'], 'uses'=>'AssignFeeController@headwiseTotalReport']);

Route::get('defaulter_list', ['as'=>'defaulter_list',  'middleware'=>['ability:super-admin|account,defaulter-list','auth'],'uses'=>'AssignFeeController@defaulter_list']);

Route::post('defaulter_list', ['as'=>'defaulter_list', 'middleware'=>['ability:super-admin|account,defaulter-list','auth'], 'uses'=>'AssignFeeController@defaulter_list']);

Route::post('defaulter_notification', ['as'=>'defaulter_notification', 'middleware'=>['ability:super-admin|account,defaulter-list','auth'], 'uses'=>'AssignFeeController@defaulter_notification']);

/*Route::post('noDuesHeadWise', ['as'=>'nodues_headwise', 'uses'=>'AssignFeeController@noDuesHeadWise']);*/



Route::get('online_register', 'OnlineRegisterController@index')->name('online_register');
Route::post('online_register', 'OnlineRegisterController@register_online')->name('online_register');
Route::post('/online_branch_select', 'OnlineRegisterController@branch_select')->name('online_branch_select');
Route::post('/online_amount', 'OnlineRegisterController@online_amount')->name('online_amount');


Route::get('/branch', 'BranchController@index')->name('branch');
Route::get('/branch/{id?}', 'BranchController@index');
Route::post('/branch_ops', 'BranchController@branch_ops')->name('branch_ops');

Route::get('/payment_type', 'BranchController@payment_type')->name('payment_type');
Route::get('/payment_type/{id?}', 'BranchController@payment_type');
Route::post('payment_type', 'BranchController@payment_ops')->name('payment_type');


Route::get('/branch', 'BranchController@index')->name('branch');
Route::get('/branch/{id?}', 'BranchController@index');
Route::post('/branch_ops', 'BranchController@branch_ops')->name('branch_ops');

Route::get('/payment_type', 'BranchController@payment_type')->name('payment_type');
Route::get('/payment_type/{id?}', 'BranchController@payment_type');
Route::post('payment_type', 'BranchController@payment_ops')->name('payment_type');
Route::post('/transfer', 'Student\StudentController@transfer')->name('transfer');

Route::get('create_promotion', 'Student\StudentController@create_promotion')->name('create_promotion');
Route::get('switchssn/{id}', ['as'=>'.switchssn', 'uses'=>'HomeController@switch_session']);
});
Route::get('user/changePassword','UserController@changePassword')->name('user.changePassword');
Route::post('user/changePassword','UserController@changePassword')->name('user.changePassword');
Route::get('update_tarnsactions','Account\Transaction\TransactionController@Update_tr');

/*ZOOM MEETINGS*/
Route::group(['prefix'=>'live_class', 'as'=>'live_class','namespace'=>'LiveClass\\'],function(){
  Route::get('/schedule',[ 'as'=>'.schedule',  'middleware' => ['ability:super-admin|account,live-class-schedule'],'uses'=>'ZoomMeetingController@index']); 
  Route::get('/observe_class',['as'=>'.observe_class',    'middleware' => ['ability:super-admin|account,live-class-observe'],'uses'=>'ZoomMeetingController@observe']); 
  Route::get('/host_class/{meeting_id}/{meeting_name}','ZoomMeetingController@host_class')->name('.host_class');
  Route::get('/list_attendance/{meeting_id}','ZoomMeetingController@meeting_attendance')->name('.list_attendance');
  Route::get('/delete/{meeting_id}','ZoomMeetingController@delete_class')->name('.delete');
  // Route::get('/observe_class','ZoomMeetingController@observe')->name('.observe_class');
  Route::get('/observe/{meeting_id}','ZoomMeetingController@observe')->name('.observe');
  
});
Route::post('live_class/store','Api\ServicesController@create_zoom_meeting')->name('live_class.store');

Route::group(['prefix'=>'internal_meeting','as'=>'internal_meeting','namespace'=>'InternalMeeting\\'],function(){
  Route::get('/',[ 'middleware' => ['ability:super-admin|account,internal-meeting'],'uses'=>'InternalMeetingController@index']);
  Route::get('/host/{meeting_id}','InternalMeetingController@host_meeting')->name('.host');
  Route::get('/join/{meeting_id}','InternalMeetingController@join_meeting')->name('.join');
  Route::get('/delete/{meeting_id}','InternalMeetingController@delete_meeting')->name('.delete');
});
Route::post('internal_meeting/store','Api\ServicesController@create_internal_meeting')->name('internal_meeting.store');

/*EXAM AJAX ROUTES*/
Route::post('loadExamType','Exam\ExamCreateController@getExamType')->name('loadExamType');
Route::post('loadExamSubject','Exam\ExamCreateController@getSubject')->name('loadExamSubject');
Route::post('loadExamsByMode','Exam\ExamAssessmentController@getExamByMode')->name('loadExamsByMode');
//RETURN URL PAYMENT GATEWAY
Route::post('/feeReceiveIcici','OnlineAdmission\OnlineAdmissionController@EazyPayResponse')->name('feeReceiveIcici');

Route::get('test','Exam\ExamCreateController@test')->name('test');

//EAZYPAY STUDENT END
Route::post('/fees/eazypay-form','Account\PaymentController@EazyPayForm')->name('fees.eazypay-form');
Route::get('/fees/eazypay-payment','Account\PaymentController@EazyPayPayment')->name('fees.eazypay-payment');
Route::post('/fees/eazypay-response','Account\PaymentController@eazyPayResponse')->name('fees.eazypay-response');
Route::get('/fees/payment-status/{txnid}','Account\PaymentController@paymentStatus')->name('fees.payment-status');

/*MISCELLANIOUS ROUTES*/

Route::group(['prefix'=>'miscellaneous/', 'as'=>'miscellaneous','namespace'=>'Miscellaneous\\'],function(){
    /*Fee Head*/
    Route::get('head',                    ['as' => '.head',                  'middleware' =>['ability:super-admin|account,fee-head-index','auth'],                  'uses' => 'MiscellaneousHeadController@index']);
    Route::post('head/store',             ['as' => '.head.store',            'middleware' =>['ability:super-admin|account,fees-head-add','auth'],                    'uses' => 'MiscellaneousHeadController@store']);
    Route::get('head/{id}/edit',          ['as' => '.head.edit',             'middleware' =>['ability:super-admin|account,fees-head-edit','auth'],                   'uses' => 'MiscellaneousHeadController@edit']);
    Route::post('head/{id}/update',       ['as' => '.head.update',           'middleware' =>['ability:super-admin|account,fees-head-edit','auth'],                   'uses' => 'MiscellaneousHeadController@update']);
    Route::get('head/{id}/delete',        ['as' => '.head.delete',           'middleware' =>['ability:super-admin|account,fees-head-delete','auth'],                 'uses' => 'MiscellaneousHeadController@delete']);
    Route::get('head/{id}/active',        ['as' => '.head.active',           'middleware' =>['ability:super-admin|account,fees-head-active','auth'],                 'uses' => 'MiscellaneousHeadController@Active']);
    Route::get('head/{id}/in-active',     ['as' => '.head.in-active',        'middleware' =>['ability:super-admin|account,fees-head-in-active','auth'],              'uses' => 'MiscellaneousHeadController@inActive']);
    Route::post('head/bulk-action',       ['as' => '.head.bulk-action',      'middleware' =>['ability:super-admin|account,fees-head-bulk-action','auth'],            'uses' => 'MiscellaneousHeadController@bulkAction']);
    /*Assign Fee*/
    Route::get('newAssignList',                    ['as' => '.newAssignList',                  'middleware' =>['ability:super-admin|account,assign-fee-index','auth'],                  'uses' => 'MiscellaneousAssignFeeController@new_index']);
    Route::post('/new_assign_fee', 'MiscellaneousAssignFeeController@new_assigned')->name('.new_assign_fee');
    Route::get('/assignFee/{id}/delete', 'MiscellaneousAssignFeeController@deleteAssignFee')->name('.assignFee.delete');
    Route::post('/loadAssignFees', 'MiscellaneousAssignFeeController@loadAssignFees')->name('.loadAssignFees');
    Route::post('/edit/assignfee', 'MiscellaneousAssignFeeController@editAssignFees')->name('.edit.assignfee');

    Route::get('/collect_fee', [ 'as'=>'.collect_fee', 'middleware' =>['ability:super-admin|account,  collect-fee-index','auth'], 'uses'=>'MiscellaneousAssignFeeController@collect']);

    Route::post('/collect_fee', ['as'=>'.collect_fee','middleware' =>['ability:super-admin|account,  collect-fee-index','auth'],'uses'=>'MiscellaneousAssignFeeController@collect']);

    Route::get('/studentfeeReceipt/{receipt_no}', 'MiscellaneousAssignFeeController@feeReceipt')->name('.studentfeeReceipt');
    Route::post('/student_fee', 'MiscellaneousAssignFeeController@student_fee')->name('.student_fee');
    Route::post('/get_std_id', 'MiscellaneousAssignFeeController@get_std_id')->name('.get_std_id');
    Route::post('/student_fee_history', 'MiscellaneousAssignFeeController@student_fee_history')->name('.student_fee_history');

    Route::get('/collection_List', ['as'=>'.collection_List', 'middleware' =>['ability:super-admin|account,  mis-collection-list','auth'],'uses'=>'MiscellaneousAssignFeeController@collection_List']);

    Route::get('/discount_status', 'MiscellaneousAssignFeeController@discount_status')->name('.discount_status');
    Route::post('/change_discount_status', 'MiscellaneousAssignFeeController@change_discount_status')->name('.change_discount_status');

    Route::get('/edit/fee_collection/{id}',       ['as' => '.edit.fee_collection',           'middleware'=>'auth',['ability:super-admin|account'],                 'uses' => 'MiscellaneousAssignFeeController@edit_collection']);
    Route::post('/edit/fee_collection/{id}',       ['as' => '.edit.fee_collection',           'middleware'=>'auth',['ability:super-admin|account'],                 'uses' => 'MiscellaneousAssignFeeController@edit_collection']);
    /*Route::get('/edit/fee_collection/{id}', 'AssignFeeController@edit_collection')->name('edit.fee_collection');*/
    Route::get('/delete/fee_collection/{id}',       ['as' => '.delete.fee_collection',           'middleware'=>'auth',['ability:super-admin'],                 'uses' => 'MiscellaneousAssignFeeController@delete_collection']);

    Route::get('/cancel/fee_receipt/{id}',       ['as' => '.cancel.fee_receipt',           'middleware'=>['ability:super-admin|account,mis-cancel-receipt','auth'],                 'uses' => 'MiscellaneousAssignFeeController@cancel_receipt']);
    Route::get('/cancelled_receipts', 'MiscellaneousAssignFeeController@cancelled_receipts')->name('.cancelled_receipts');
});
Route::get('change_password_all',       ['as' => '.change_password',      'middleware' => ['ability:super-admin|super-admin,super-admin'],          'uses' => 'UserController@changePasswordAll']);


// Ankur Routes
Route::get('monthly_statement', ['as'=>'monthly_statement','middleware'=>['ability:super-admin|account,monthly-collection-statement','auth'], 'uses'=>'AssignFeeController@report_card']);
Route::post('monthly_statement', ['as'=>'monthly_statement','middleware'=>['ability:super-admin|account,monthly-collection-statement','auth'], 'uses'=>'AssignFeeController@report_card']);
Route::get('report_head', ['as'=>'report_head', 'uses'=>'AssignFeeController@report_head']);
Route::post('report_head', ['as'=>'report_head', 'uses'=>'AssignFeeController@report_head']);
Route::get('class_wise_due_statement', ['as'=>'class_wise_due_statement','middleware'=>['ability:super-admin|account,class-wise-due-statement','auth'], 'uses'=>'AssignFeeController@report_class']);
Route::post('class_wise_due_statement', ['as'=>'class_wise_due_statement','middleware'=>['ability:super-admin|account,class-wise-due-statement','auth'], 'uses'=>'AssignFeeController@report_class']);

// Ankur Route End

/*student collection Report*/
Route::get('student_collection_report', ['as'=>'student_collection_report','middleware'=>['ability:super-admin|account,student-collection-report','auth'], 'uses'=>'AssignFeeController@student_collection_report']);
Route::post('student_collection_report', ['as'=>'student_collection_report','middleware'=>['ability:super-admin|account,student-collection-report','auth'], 'uses'=>'AssignFeeController@student_collection_report']);
/*student collection Report*/

/* Career ROUTES*/
Route::group(['prefix'=>'career/',  'as'=>'career', 'namespace'=>'career\\'], function(){
  Route::get('/','CareerController@index');
  Route::post('/','CareerController@store')->name('career');
  
  Route::get('list', ['as'=>'.list','middleware'=>['ability:super-admin|account,career-list','auth'], 'uses'=>'CareerController@list']);
  
  Route::get('{id}/view',                 ['as' => '.view',                    'middleware' => ['ability:super-admin|account,student-list','auth'],                   'uses' => 'CareerController@CareerView']);
     Route::get('{id}/{status}/ChangeStatus',                 ['as' => '.ChangeStatus',                    'middleware' => ['ability:super-admin|account,student-list','auth'],                   'uses' => 'CareerController@ChangeStatus']);
  Route::get('{id}/followup',                 ['as' => '.followup',                    'middleware' => ['ability:super-admin|account,career-list','auth'],                   'uses' => 'CareerController@CareerAddFollowup']);
      Route::post('followupStore',                 ['as' => '.followupStore',                    'middleware' => ['ability:super-admin|account,career-list','auth'],                   'uses' => 'CareerController@CareerFollowupStore']);
      Route::get('{id}/followup/delete',                 ['as' => '.followup.delete',                    'middleware' => ['ability:super-admin|account,career-list','auth'],                   'uses' => 'CareerController@CareerFollowupDelete']);

      Route::get('{id}/delete',                 ['as' => '.delete',                    'middleware' => ['ability:super-admin|account,career-list','auth'],                   'uses' => 'CareerController@CareerDelete']);
      // career enquiry followup
});


Route::get('bulkEditCollection', ['as'=>'bulkEditCollection','middleware'=>['ability:super-admin|account,bulk-edit-collection','auth'], 'uses'=>'AssignFeeController@bulkEditCollection']);
Route::post('bulkEditCollection', ['as'=>'bulkEditCollection','middleware'=>['ability:super-admin|account,bulk-edit-collection','auth'], 'uses'=>'AssignFeeController@bulkEditCollection']);
Route::post('loadAssignFee','AssignFeeController@LoadCourseFeeHeads')->name('loadAssignFee');

Route::post('bulkUpdateCollection', ['as'=>'bulkUpdateCollection','middleware'=>['ability:super-admin|account,bulk-edit-collection','auth'], 'uses'=>'AssignFeeController@bulkUpdateCollection']);



Route::get('importTransportcollection', ['as'=>'importTransportcollection','middleware'=>['ability:super-admin|account,bulk-edit-collection','auth'], 'uses'=>'Transport\ReportController@ImportCollection']);

Route::group(['prefix'=>'Online-Enquiry','as'=>'online_enquiry','namespace'=>'online_enquiry\\'],function(){
  Route::get('/','onlinequeiryController@index');
  Route::post('/','onlinequeiryController@store')->name('online_enquiry');

});

Route::group(['prefix'=>'EnableFine','as'=>'enableFine','middleware'=>'auth','namespace'=>'EnableFine\\'],function(){

    Route::get('',            ['as' => '',             'middleware' => ['ability:super-admin|account,fine-settings'],               'uses' => 'FineController@index']);

    Route::post('store',            ['as' => '.store',             'middleware' => ['ability:super-admin|account,fine-settings'],               'uses' => 'FineController@store']);

    Route::get('delete/{id}',            ['as' => '.delete',             'middleware' => ['ability:super-admin|account,fine-settings'],               'uses' => 'FineController@delete']);

  });

  Route::post('EnableFine/getHead','EnableFine\FineController@getHead')->name('enableFine.getHead');
  
  
  
  Route::group(['prefix'=>'Lms/', 'as'=>'Lms','middleware'=>'auth','namespace'=>'Lms\\'],function(){
    /*Fee Head*/
    Route::get('Lesson_plans',                    ['as' => '.Lesson_plans',                  'middleware' =>['ability:super-admin|account,lesson-plan','auth'],    'uses' => 'Lesson_plansController@index']);

// micro_planner
     Route::get('micro_planner',                    ['as' => '.micro_planner',                  'middleware' =>['ability:super-admin|account,micro-planner','auth'],    'uses' => 'Micro_plannerController@index']);

     Route::get('micro_planner/add',                ['as' => '.micro_planner.add',   'middleware' => ['ability:super-admin|account,micro-planner','auth'], 'uses' =>'micro_plannerController@add']);

     Route::post('micro_planner/store',             ['as' => '.micro_planner.store',            'middleware' => ['ability:super-admin|account,micro-planner','auth'],                'uses' => 'micro_plannerController@store']);

     Route::get('micro_planner/{id}/edit',          ['as' => '.micro_planner.edit',             'middleware' => ['ability:super-admin|account,micro-planner','auth'],                'uses' => 'micro_plannerController@edit']);

    Route::post('micro_planner/{id}/edit',          ['as' => '.micro_planner.edit',             'middleware' => ['ability:super-admin|account,micro-planner','auth'],                'uses' => 'micro_plannerController@edit']);

    Route::get('micro_planner/{id}/delete',          ['as' => '.micro_planner.delete',             'middleware' => ['ability:super-admin|account,micro-planner','auth'],                'uses' => 'micro_plannerController@delete']);

// end
     // macro_planner
     Route::get('macro_planner',                    ['as' => '.macro_planner',                  'middleware' =>['ability:super-admin|account,macro-planner','auth'],    'uses' => 'macro_plannerController@index']);

     Route::get('macro_planner/add',                ['as' => '.macro_planner.add',   'middleware' => ['ability:super-admin|account,macro-planner','auth'], 'uses' =>'macro_plannerController@add']);

     Route::post('macro_planner/store',             ['as' => '.macro_planner.store',            'middleware' => ['ability:super-admin|account,macro-planner','auth'],                'uses' => 'macro_plannerController@store']);

      Route::get('macro_planner/{id}/edit',          ['as' => '.macro_planner.edit',             'middleware' => ['ability:super-admin|account,macro-planner','auth'],                'uses' => 'macro_plannerController@edit']);

    Route::post('macro_planner/{id}/edit',          ['as' => '.macro_planner.edit',             'middleware' => ['ability:super-admin|account,macro-planner','auth'],                'uses' => 'macro_plannerController@edit']);

    Route::get('macro_planner/{id}/delete',          ['as' => '.macro_planner.delete',             'middleware' => ['ability:super-admin|account,macro-planner','auth'],                'uses' => 'macro_plannerController@delete']);

// end

    Route::get('Lesson_plans/add',                ['as' => '.Lesson_plans.add',   'middleware' => ['ability:super-admin|account,lesson-plan','auth'], 'uses' =>'Lesson_plansController@add']);
    Route::post('Lesson_plans/store',             ['as' => '.Lesson_plans.store',            'middleware' => ['ability:super-admin|account,lesson-plan','auth'],                'uses' => 'Lesson_plansController@store']);
    
    Route::get('Lesson_plans/{id}/edit',          ['as' => '.Lesson_plans.edit',             'middleware' => ['ability:super-admin|account,lesson-plan','auth'],                'uses' => 'Lesson_plansController@edit']);

    Route::post('Lesson_plans/{id}/edit',          ['as' => '.Lesson_plans.edit',             'middleware' => ['ability:super-admin|account,lesson-plan','auth'],                'uses' => 'Lesson_plansController@edit']);

    Route::get('Lesson_plans/{id}/delete',          ['as' => '.Lesson_plans.delete',             'middleware' => ['ability:super-admin|account,lesson-plan','auth'],                'uses' => 'Lesson_plansController@delete']);


    Route::post('Lesson_plans/find-semester',                    ['as' => '.Lesson_plans.find-semester',                                                           'uses' => 'Lesson_plansController@findSemester']);
    Route::post('Lesson_plans/mark-ledger/find-subject',         ['as' => '.Lesson_plans.find-subject',                                                               'uses' => 'Lesson_plansController@findSubject']);

    Route::post('Lesson_plans/find-chapter',                   ['as' => '.Lesson_plans.find-chapter',                                                           'uses' => 'Lesson_plansController@findChapter']);
    Route::post('Lesson_plans/changeStatus',        ['as' => '.Lesson_plans.changeStatus',           'middleware' => ['ability:super-admin|account,lesson-plan','auth'],               'uses' => 'Lesson_plansController@Statuss']);

    
    Route::get('Econtent',                    ['as' => '.Econtent',                  'middleware' =>['ability:super-admin|account,econtent','auth'],    'uses' => 'EContentController@index']);
    Route::post('Econtent',                    ['as' => '.Econtent',                  'middleware' =>['ability:super-admin|account,econtent','auth'],    'uses' => 'EContentController@index']);
    Route::get('Econtent/add',                ['as' => '.Econtent.add',   'middleware' => ['ability:super-admin|account,econtent','auth'], 'uses' =>'EContentController@add']);

    Route::post('Econtent/store',             ['as' => '.Econtent.store',            'middleware' => ['ability:super-admin|account,econtent','auth'],                'uses' => 'EContentController@store']);
    
    Route::get('Econtent/{id}/edit',          ['as' => '.Econtent.edit',             'middleware' => ['ability:super-admin|account,econtent','auth'],                'uses' => 'EContentController@edit']);
    Route::post('Econtent/{id}/edit',          ['as' => '.Econtent.edit',             'middleware' => ['ability:super-admin|account,econtent','auth'],                'uses' => 'EContentController@edit']);
    Route::get('Econtent/{id}/delete',          ['as' => '.Econtent.delete',             'middleware' => ['ability:super-admin|account,econtent','auth'],                'uses' => 'EContentController@delete']);

    Route::post('Econtent/find-semester',                    ['as' => '.Econtent.find-semester',                                                           'uses' => 'EContentController@findSemestr']);

    Route::post('Econtent/mark-ledger/find-subject',         ['as' => '.Econtent.find-subject',                                                               'uses' => 'EContentController@findSubjet']);


    Route::post('Econtent/changeStatus',        ['as' => '.Econtent.changeStatus',           'middleware' => ['ability:super-admin|account,econtent','auth'],               'uses' => 'EContentController@Statuss']);
    
    Route::post('Econtent/find-chapter',                   ['as' => '.Econtent.find-chapter',                                                           'uses' => 'EContentController@findChapter']);

     Route::post('Econtent/chapter',        ['as' => '.Econtent.chapter',           'middleware' => ['ability:super-admin|account,econtent','auth'],               'uses' => 'EContentController@chapter']);
     Route::post('Econtent/topic',        ['as' => '.Econtent.topic',           'middleware' => ['ability:super-admin|account,econtent','auth'],               'uses' => 'EContentController@topic']);
  });

// <!-- master chapter /worksheet-->
  Route::get('lms_master',                    ['as' => 'lms_master',                  'middleware' => ['ability:super-admin|account,subject-master-index','auth'],             'uses' => 'WarksheetController@index']);

  Route::post('lms_master/store',             ['as' => 'lms_master.store',            'middleware' => ['ability:super-admin|account,addsubject-index'],               'uses' => 'WarksheetController@store']);

  Route::get('lms_master/{id}/edit',          ['as' => 'lms_master.edit',             'middleware' => ['ability:super-admin|account,lms_master-edit'],              'uses' => 'WarksheetController@edit']);

    Route::post('lms_master/{id}/edit',       ['as' => 'lms_master.edit',           'middleware' => ['ability:super-admin|account,lms_master-edit'],              'uses' => 'WarksheetController@edit']);

    Route::get('lms_master/{id}/delete',        ['as' => 'lms_master.delete',           'middleware' => ['ability:super-admin|account,lms_master-delete'],            'uses' => 'WarksheetController@delete']);

  Route::get('chapter_master',                    ['as' => 'chapter_master',                  'middleware' => ['ability:super-admin|account,lesson-plan','auth'],             'uses' => 'ChapterController@index']);
  Route::get('chapter_master/add',               ['as'=>'.chapter_master.add',                'middleware' =>['ability:super-admin|account,lesson-plan','auth'],                   'uses'=>'ChapterController@add']);
   Route::post('chapter_master/store',             ['as' => 'chapter_master.store',            'middleware' => ['ability:super-admin|account,lesson-plan'],               'uses' => 'ChapterController@store']);
  Route::get('chapter_master/{id}/edit',          ['as' => 'chapter_master.edit',             'middleware' => ['ability:super-admin|account,lesson-plan'],              'uses' => 'ChapterController@edit']);

  Route::post('chapter_master/{id}/edit',          ['as' => 'chapter_master.edit',             'middleware' => ['ability:super-admin|account,lesson-plan'],              'uses' => 'ChapterController@edit']);
  Route::get('chapter_master/{id}/delete',        ['as' => 'chapter_master.delete',           'middleware' => ['ability:super-admin|account,lesson-plan'],            'uses' => 'ChapterController@delete']);
  