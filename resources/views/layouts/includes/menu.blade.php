
<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse ace-save-state hidden-print">
    <script type="text/javascript">
        try{ace.settings.loadState('sidebar')}catch(e){}
    </script>
    

<?php
    $role = auth()->user()->userRole[0]->id;
    $arr = \DB::table('permission_role')->select('per.id as per_id','per.name as per_name','r.id as role_id','r.name as role')
    ->leftJoin('permissions as per','per.id','=','permission_role.permission_id')
    ->leftJoin('roles as r','r.id','=','permission_role.role_id')
    ->where('permission_role.role_id',$role)
    ->get();
    
    // global $r ;
    // global $p;
    
    $r = [];
    $p = [];
    foreach($arr as $k => $v){
        $r[$v->role_id] = $v->role;
        $p[$v->per_id] = $v->per_name;
    }
    
    function check_ab($r,$p,$role,$permission){
       $ret = false;
       $roles = explode(',',$role);
       foreach($roles as $v){
           if(in_array($v,$r)){
               return true;
           }
       }
       
       $per = explode(',',$permission);
       
       foreach($per as $v){
           if(in_array($v,$p)){
               return true;
           }
       }
       
       return false;
    }
    

?>

    <ul class="nav nav-list">
        {{-- Dashboard --}}
        <li class="{!! request()->is('/')?'active':'' !!}">
            <a href="{{ route('home') }}" >
                <i class="menu-icon fa fa-tachometer"></i>
                <span class="menu-text"> Dashboard </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
        </li>
        {{-- Staff & Student --}}
        <?php $ret = check_ab($r,$p,'super-admin,account','frontdesk,enquiry,enquiry-form,enquiry-list,enquiry-report,prospectus,prospectus-sale,prospectus-list,visitor-book,call-log,postal,postal-dispatch,postal-received,postal-list,complaint-list');if($ret){ ?>
        <li class="{!! request()->is('enquiry*')||request()->is('admission*')||request()->is('frontdesk*')?'active open':'' !!}  hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-user" aria-hidden="true"></i>
                <span class="menu-text"> Front Desk </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
              <?php $ret = check_ab($r,$p,'super-admin,account','enquiry,enquiry-form,enquiry-list,enquiry-report');if($ret){ ?>
                <li class="{!! request()->is('enquiry')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Enquiry
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <?php $ret = check_ab($r,$p,'super-admin,account','enquiry-form');if($ret){ ?>
                        <li class="{!! request()->is('enquiry')?'active':'' !!} hover">
                            <a href="{{ route('.enquiry') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Enquiry Form
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <?php } ?>
                        <?php $ret = check_ab($r,$p,'super-admin,account','enquiry-list');if($ret){ ?>
                        <li class="{!! request()->is('enquirylist')?'active':'' !!} hover">
                            <a href="{{ route('.enquiry_list') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Enquiry List
                                <b class="arrow fa fa-angle-r"></b>
                            </a>
                        </li>
                        <?php } ?>
                       <?php $ret = check_ab($r,$p,'super-admin,account','enquiry-report');if($ret){ ?>
                        <li class="{!! request()->is('enquirystatus')?'active':'' !!} hover">
                            <a href="{{ route('.enquiry_status') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Enquiry Report
                                <b class="arrow fa fa-angle-r"></b>
                            </a>
                        </li>
                        <?php } ?>
                        
                    </ul>
                </li>
                <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','prospectus,prospectus-sale,prospectus-list');if($ret){ ?>
                <li class="hover">
                    <a href="#" class="dropdown-toggle">
                        <span class="menu-text"> Registration </span>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <?php $ret = check_ab($r,$p,'super-admin,account','prospectus-sale');if($ret){ ?>
                        <li class="{!! request()->is('assignment')?'active':'' !!} hover">
                            <a href="{{ route('.admission') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Registration Form
                            </a>
                            <b class="arrow"></b>
                        </li>
                         <?php } ?>

                        <?php $ret = check_ab($r,$p,'super-admin,account','prospectus-list');if($ret){ ?>
                        <li class="{!! request()->is('admission_list*')?'active':'' !!} hover">
                            <a href="{{ route('.admission_list') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Registration List
                                <b class="arrow fa fa-angle-r"></b>
                            </a>
                        </li>
                         <?php } ?>
                    </ul>
                </li>
                <?php } ?>
                <?php $ret = check_ab($r,$p,'super-admin,account','visitor-book');if($ret){ ?>
                <li class="{!! request()->is('frontdesk/visitor')?'active':'' !!} hover">
                    <a href="{{ route('frontdesk.visitor')}}">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Visitor Book
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','call-log');if($ret){ ?>
                <li class="{!! request()->is('frontdesk/callLog')?'active':'' !!} hover">
                    <a href="{{route('frontdesk.callLog')}}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Call Logs
                    </a>
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','postal,postal-dispatch,postal-received,postal-list');if($ret){ ?>
                <li class="{!! request()->is('frontdesk/postal*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Postal 
                    </a>
                     <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','postal-dispatch');if($ret){ ?>
                        <li class="{!! request()->is('frontdesk/postal/dispatch')?'active':'' !!}hover">
                            <a href="{{route('frontdesk.postal.dispatch')}}">
                                <!-- <i class="menu-icon fa fa-caret-right"></i> -->
                                Dispatch
                            </a>
                        </li>
                         <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','postal-received');if($ret){ ?>
                         <li class=" {!! request()->is('frontdesk/postal/receive')?'active':'' !!}hover">
                            <a href="{{route('frontdesk.postal.receive')}}">
                                <!-- <i class="menu-icon fa fa-caret-right"></i> -->
                                Received
                            </a>
                        </li>
                           <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','postal-list');if($ret){ ?>
                        <li class=" {!! request()->is('frontdesk/postal')?'active':'' !!}hover">
                            <a href="{{route('frontdesk.postal')}}">
                                <!-- <i class="menu-icon fa fa-caret-right"></i> -->
                               Postal List
                            </a>
                        </li>
                           <?php } ?>
                    </ul>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','complaint-list');if($ret){ ?>
                <li class="{!! request()->is('frontdesk/complain')?'active':'' !!} hover">
                    <a href="{{route('frontdesk.complain')}}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Complain
                    </a>
                </li>
                  <?php } ?>
            </ul>
        </li>
        <?php } ?>
        {{--Admission Form--}}
       
        
       
          <?php $ret = check_ab($r,$p,'super-admin,account','student-detail,promote-student,promote-student-bulk,transfer-student,student-import,student-import-student,student-import-fee,student-login-detail,student-login-detail-generate-password,student-view-login-detail,student-registration,student-view,student-edit,student-delete,student-active,  student-in-active,student-bulk-action');if($ret){ ?>
        <li class="{!! request()->is('student*')?'active open':'' !!}  hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-users" aria-hidden="true"></i>
                <span class="menu-text"> Student</span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                  <?php $ret = check_ab($r,$p,'super-admin,account','student-detail,student-view,student-edit,student-delete,student-active,student-in-active,student-bulk-action');if($ret){ ?>
                <li class="{!! request()->is('student')?'active':'' !!} hover">
                    <a href="{{ route('student') }}" accesskey="S">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Student Detail
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','student-registration,student-import,student-import-student');if($ret){ ?>
                <li class="{!! request()->is('student/registration')?'active':'' !!} hover">
                    <a href="{{ route('student.registration') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Admission
                    </a>

                    <b class="arrow"></b>
                </li>
                  <?php } ?>
                  <!--bulk edit student -->
                  <?php $ret = check_ab($r,$p,'super-admin,account','bulk_edit_student');if($ret){ ?>
                <li class="{!! request()->is('student/bulk_edit_student')?'active':'' !!} hover">
                    <a href="{{ route('student.bulk_edit_student') }}" accesskey="S">
                        <i class="menu-icon fa fa-caret-right"></i>
                         Bulk Student Edit
                    </a>
            
                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                 <!-- bulk edit student -->
                 <?php $ret = check_ab($r,$p,'super-admin,account','promote-student');if($ret){ ?>
                <li class="{!! request()->is('student/transfer')?'active':'' !!} hover">
                    <a href="{{ route('student.transfer') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Promote Student
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','promote-student-bulk');if($ret){ ?>
                <li class="{!! request()->is('student/transfer/bulk')?'active':'' !!} hover">
                    <a href="{{ route('student.transfer.bulk') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Promote Student (Bulk)
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','transfer-student');if($ret){ ?>
                <li class="{!! request()->is('student/transfer-student')?'active':'' !!} hover">
                    <a href="{{ route('student.transfer-student') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Transfer Student
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','student-import,student-import-student,student-import-fee');if($ret){ ?>
                <li class="{!! request()->is('student/import-student') || request()->is('student/import-assign-fee')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <span class="menu-text"> Import</span>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','student-import-student');if($ret){ ?>
                        <li class="{!! request()->is('student/import-student')?'active':'' !!} hover">
                            <a href="{{ route('student.import-student') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Import Student
                            </a>
                            <b class="arrow"></b>
                        </li>
                         <?php } ?>

                         <?php $ret = check_ab($r,$p,'super-admin,account','student-import-fee');if($ret){ ?>
                        <li class="{!! request()->is('student/import-fee')?'active':'' !!} hover">
                            <a href="{{ route('student.import-fee') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Import Fee
                                <b class="arrow fa fa-angle-r"></b>
                            </a>
                        </li>
                         <?php } ?>
                    </ul>
                </li>
                 <?php } ?>
                
                  <?php $ret = check_ab($r,$p,'super-admin,account','student-login-detail,student-login-detail-generate-password,student-view-login-detail');if($ret){ ?>
                <li class="{!! request()->is('student/import-student') || request()->is('student/import-assign-fee')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <span class="menu-text"> Login Details</span>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','student-login-detail-generate-password');if($ret){ ?>
                        <li class="{!! request()->is('student/')?'active':'' !!} hover">
                            <a href="{{ route('student.generatepassword') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Generate Passowrd
                            </a>
                            <b class="arrow"></b>
                        </li>
                           <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','student-view-login-detail');if($ret){ ?>
                       
                        <li class="{!! request()->is('student/logindetail')?'active':'' !!} hover">
                            <a href="{{ route('student.logindetail') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               View Login Detail
                                <b class="arrow fa fa-angle-r"></b>
                            </a>
                        </li>
                          <?php } ?>
                    </ul>
                </li>
                 <?php } ?>
                 
                <!--li class="{!! request()->is('student/document')?'active':'' !!} hover">
                    <a href="{{ route('student.document') }}"  accesskey="U">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Document Upload
                    </a>
                    <b class="arrow"></b>
                </li>

                <li class="{!! request()->is('student/note')?'active':'' !!} hover">
                    <a href="{{ route('student.note') }}">
                        <i class="menu-icon fa fa-caret-right"  accesskey="N"></i>
                        Create Notes
                    </a>
                    <b class="arrow"></b>
                </li -->
            </ul>
        </li>
          <?php } ?>
         

        {{-- Account --}}
         <?php $ret = check_ab($r,$p,'super-admin,account','fees-head-index,fees-head-add,fees-head-edit,fees-head-delete,fees-head-active,fees-head-in-active,fees-head-bulk-action,fee-new-assign-list,new-assign-fee,assign-fee-edit,assign-fee-delete,fee-collection,collect-fees,bulk-collect-fees,collection-list,discount-status,cancel-receipt,defaulter-list,  fee-reports,due-report,report-head-collection-due,report-payment-type,report-month-wise,report-no-due-headwise-cumulative,report-student-headwise,report-student-monthwise, no-due-month-wise,fees-edit-collection,fees-delete-collection,bulk-add-discount');if($ret){ ?>
        <li class="{!! request()->is('collection_List') || request()->is('bulkEditCollection*') ||request()->is('discount_status*')||request()->is('cancelled_receipts*')||request()->is('collect_fee*')||request()->is('newAssignList*')||request()->is('dueReport*')||request()->is('feeReport*')||request()->is('feeReportMonthWise*')||request()->is('noDues*')||request()->is('noDuesMonthWise*')||request()->is('noDues_student*')||request()->is('noDues_studentMonthWise*')||request()->is('bulk_collect_fee*')||request()->is('assign_fee_list*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-inr" aria-hidden="true"></i>
                <span class="menu-text"> Fees </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
               <?php $ret = check_ab($r,$p,'super-admin,account','fees-head-index,fees-head-add,fees-head-edit,fees-head-delete,fees-head-active,fees-head-in-active,fees-head-bulk-action');if($ret){ ?>
                <li class="{!! request()->is('account/fees/head')?'active':'' !!}  hover">
                    <a href="{{ route('account.fees.head') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Fees Head
                    </a>

                    <b class="arrow"></b>
                </li>
                   <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','fee-new-assign-list,new-assign-fee,assign-fee-edit,assign-fee-delete');if($ret){ ?>
                <li class="{!! request()->is('newAssignList*')?'active':'' !!} hover">
                    <a href="{{ route('newAssignList') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Assign Fees
                    </a>
                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                 
                 <?php $ret = check_ab($r,$p,'super-admin,account','fee-new-assign-list,new-assign-fee,assign-fee-edit,assign-fee-delete');if($ret){ ?>
                <li class="{!! request()->is('assign_fee_list*')?'active':'' !!} hover">
                    <a href="{{ route('assign_fee_list') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Assign Fees Listing
                    </a> 
                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','fee-collection,collect-fees,bulk-collect-fees');if($ret){ ?>
                <li class="{!! request()->is('collect_fee*')||request()->is('bulk_collect_fee*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                         Fee Collection
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','collect-fees');if($ret){ ?>
                        <li class="{!! request()->is('collect_fee*')?'active':'' !!} hover">
                            <a href="{{ route('collect_fee') }}">
                               <i class="menu-icon fa fa-caret-right"></i>
                                Collect Fees
                               <b class="arrow fa fa-angle-r"></b>
                            </a>
                
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','bulk-collect-fees');if($ret){ ?>
                        <li class="{!! request()->is('bulk_collect_fee*')?'active':'' !!} hover">
                            <a href="{{ route('bulk_collect_fee') }}">
                               <i class="menu-icon fa fa-caret-right"></i>
                                Bulk Collect Fees
                               <b class="arrow fa fa-angle-r"></b>
                            </a>
                
                        </li>
                          <?php } ?>
                    </ul>
                </li> 
                   <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','collection-list,fees-edit-collection,fees-delete-collection');if($ret){ ?>
                <li class="{!! request()->is('collection_List*')?'active':'' !!} hover">
                    <a href="{{ route('collection_List') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Collection List
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','bulk-add-discount');if($ret){ ?>
                 <li class="{!! request()->is('bulkEditCollection*')?'active':'' !!} hover">
                    <a href="{{ route('bulkEditCollection') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                         Add Discount(Bulk)
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','discount-status');if($ret){ ?>
                <li class="{!! request()->is('discount_status*')?'active':'' !!} hover">
                    <a href="{{ route('discount_status') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Discount Status
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','cancel-receipt');if($ret){ ?>
                <li class="{!! request()->is('cancelled_receipts*')?'active':'' !!} hover">
                    <a href="{{ route('cancelled_receipts') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Cancelled Receipts
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','defaulter-list');if($ret){ ?>
                <li class="{!! request()->is('defaulter_list*')?'active':'' !!} hover">
                    <a href="{{ route('defaulter_list') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Defaulter List
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','monthly-collection-statement');if($ret){ ?>
                 <li class="{!! request()->is('monthly_statement*')?'active':'' !!} hover">
                    <a href="{{ route('monthly_statement') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Due Report (Monthwise)
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','class-wise-due-statement');if($ret){ ?>
                <li class="{!! request()->is('class_wise_due_statement*')?'active':'' !!} hover">
                    <a href="{{ route('class_wise_due_statement') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Class-wise Due Statement
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                 
                 <?php } ?>
                 
                 <?php $ret = check_ab($r,$p,'super-admin,account','fee-reports,due-report,report-head-collection-due,report-payment-type,report-month-wise,report-no-due-headwise-cumulative,report-student-headwise,report-student-monthwise, no-due-month-wise,student-collection-report');if($ret){ ?>
                <li class="{!! request()->is('due_report*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Reports
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <ul class="submenu">
                            <?php $ret = check_ab($r,$p,'super-admin,account','student-collection-report');if($ret){ ?>
                                <li class="{!! request()->is('student_collection_report*')?'active':'' !!} hover">
                                    <a href="{{ route('student_collection_report') }}">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Student Collection Report
                                        <b class="arrow fa fa-angle-r"></b>
                                    </a>
                                </li>
                              <?php } ?>
                            <?php $ret = check_ab($r,$p,'super-admin,account','due-report');if($ret){ ?>
                           <li class="{!! request()->is('download*')?'active':'' !!} hover">
                                <a href="{{ route('due_report') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Due Report (Overall)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                              <?php } ?>
                              <?php  /* $ret = check_ab($r,$p,'super-admin,account','report-head-collection-due');if($ret){ ?>
                            <li class="{!! request()->is('headwiseTotalReport*')?'active':'' !!} hover">
                                <a href="{{ route('headwiseTotalReport') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Fee Head(Collection / Due)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                              <?php }  */?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-payment-type');if($ret){ ?>
                            <li class="{!! request()->is('feeReport*')?'active':'' !!} hover">
                                <a href="{{ route('fee_report') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Fee Report (Payment Type)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-month-wise');if($ret){ ?>
                            <li class="{!! request()->is('feeReportMonthWise*')?'active':'' !!} hover">
                                <a href="{{ route('fee_report_month_wise') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Fee Report (Monthwise)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                             <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-no-due-headwise-cumulative');if($ret){ ?>
                            <li class="{!! request()->is('noDues*')?'active':'' !!} hover">
                                <a href="{{ route('noDues') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    No-Due (Headwise Cumulative)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                             <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account',' no-due-month-wise');if($ret){ ?>
                            <li class="{!! request()->is('noDuesMonthWise*')?'active':'' !!} hover">
                                <a href="{{ route('noDuesMonthWise') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    No-Due (Monthwise)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                             <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-student-headwise');if($ret){ ?>
                            <li class="{!! request()->is('noDUes_student*')?'active':'' !!} hover">
                                <a href="{{ route('noDues_student') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Student (Headwise)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                             <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-student-monthwise');if($ret){ ?>
                            <li class="{!! request()->is('noDues_studentMonthWise*')?'active':'' !!} hover">
                                <a href="{{ route('noDues_studentMonthWise') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Student (MonthWise)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                               <?php } ?>
                    </ul>
                </li>
                  <?php } ?>

               
            </ul>
        </li>
         <?php } ?>

        {{-- Account --}}

         <?php $ret = check_ab($r,$p,'super-admin,account','staff-index,staff-add,staff-view,staff-edit,  staff-delete,staff-active, staff-in-active,staff-bulk-action');if($ret){ ?>
        
        <li class="{!! request()->is('staff*')?'active open':'' !!}  hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-users" aria-hidden="true"></i>
                <span class="menu-text"> Staff</span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                 <?php $ret = check_ab($r,$p,'super-admin,account','staff-index,staff-view,staff-edit,  staff-delete,staff-active, staff-in-active,staff-bulk-action');if($ret){ ?>
                <li class="{!! request()->is('staff')?'active':'' !!}  hover">
                    <a href="{{ route('staff') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Staff Detail
                    </a>

                    <b class="arrow"></b>
                </li>
                  <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','staff-add');if($ret){ ?>
                <li class="{!! request()->is('staff/add')?'active':'' !!}  hover">
                    <a href="{{ route('staff.add') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Registration
                    </a>

                    <b class="arrow"></b>
                </li>
                  <?php } ?>

                        
            </ul>
        </li>
          <?php } ?>
          <?php $ret = check_ab($r,$p,'super-admin,account','payroll-head-index,payroll-head-add,payroll-head-edit,payroll-head-delete,payroll-head-active,payroll-head-in-active ,payroll-head-bulk-action,transaction-head-index,transaction-head-add,transaction-head-edit,transaction-head-delete,transaction-head-active,transaction-head-in-active,transaction-head-bulk-action,transaction-index,transaction-add,transaction-edit,transaction-delete,transaction-active,transaction-in-active,payroll-index,payroll-balance,payroll-master-index,payroll-master-add,payroll-master-edit,payroll-master-delete,payroll-master-active,payroll-master-in-active,payroll-master-bulk-action,salary-payment-index,  salary-payment-add, salary-payment-view,salary-payment-delete');if($ret){ ?>
        <li class="{!! request()->is('account/*')?'active open':'' !!}  hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-calculator" aria-hidden="true"></i>
                <span class="menu-text"> Account</span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <!--li class="{!! request()->is('account/fees*')?'active open':'' !!} hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Fees Collection
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="{!! request()->is('account/fees')?'active':'' !!}  hover">
                            <a href="{{ route('account.fees') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Receive Detail
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('account/fees/collection')?'active':'' !!} hover">
                            <a href="{{ route('account.fees.collection') }}" accesskey="R">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Collect Fees
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('account/fees/balance')?'active':'' !!}  hover">
                            <a href="{{ route('account.fees.balance') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Balance Fees Report
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('account/fees/master/add')?'active':'' !!} hover">
                            <a href="{{ route('account.fees.master.add') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Add Fees
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('account/fees/head')?'active':'' !!}  hover">
                            <a href="{{ route('account.fees.head') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Fees Head
                            </a>

                            <b class="arrow"></b>
                        </li>

                        </ul>
                    </li -->
                 <?php $ret = check_ab($r,$p,'super-admin,account','transaction-head-index,transaction-head-add,transaction-head-edit,transaction-head-delete,transaction-head-active,transaction-head-in-active,transaction-head-bulk-action,transaction-index,transaction-add,transaction-edit,transaction-delete,transaction-active,transaction-in-active');if($ret){ ?>
                <li class="{!! request()->is('account/transaction*')?'active open':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Income/Expenses
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','transaction-index,transaction-add,transaction-edit,transaction-delete,transaction-active,transaction-in-active');if($ret){ ?>
                        <li class="{!! request()->is('account/transaction')?'active':'' !!} hover">
                            <a href="{{ route('account.transaction') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Transaction
                            </a>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','transaction-head-index,transaction-head-add,transaction-head-edit,transaction-head-delete,transaction-head-active,transaction-head-in-active,transaction-head-bulk-action');if($ret){ ?>
                        <li class="{!! request()->is('account/transaction-head')?'active':'' !!} hover">
                            <a href="{{ route('account.transaction-head') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Transaction Head
                            </a>

                            <b class="arrow"></b>
                        </li>
                           <?php } ?>
                    </ul>
                </li>
                  <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','payroll-head-index,payroll-head-add,payroll-head-edit,payroll-head-delete,payroll-head-active,payroll-head-in-active ,payroll-head-bulk-action,payroll-index,payroll-balance,payroll-master-index,payroll-master-add,payroll-master-edit,payroll-master-delete,payroll-master-active,payroll-master-in-active,payroll-master-bulk-action,salary-payment-index,  salary-payment-add, salary-payment-view,salary-payment-delete');if($ret){ ?>
                <li class="{!! request()->is('account/payroll*')?'active open':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Payroll
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','salary-payment-index,  salary-payment-add, salary-payment-view,salary-payment-delete');if($ret){ ?>
                        <li class="{!! request()->is('account/salary/payment')?'active':'' !!} hover">
                            <a href="{{ route('account.salary.payment') }}" accesskey="R">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Salary Pay
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','payroll-master-index,payroll-master-add,payroll-master-edit,payroll-master-delete,payroll-master-active,payroll-master-in-active,payroll-master-bulk-action');if($ret){ ?>
                        <li class="{!! request()->is('account/payroll/master*')?'active':'' !!} hover">
                            <a href="{{ route('account.payroll.master.add') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Add Salary
                            </a>

                            <b class="arrow"></b>
                        </li>
                         <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','payroll-balance');if($ret){ ?>
                        <li class="{!! request()->is('account/payroll/balance')?'active':'' !!}  hover">
                            <a href="{{ route('account.payroll.balance') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Balance Salary Report
                            </a>

                            <b class="arrow"></b>
                        </li>
                         <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','payroll-head-index,payroll-head-add,payroll-head-edit,payroll-head-delete,payroll-head-active,payroll-head-in-active ,payroll-head-bulk-action');if($ret){ ?>
                        <li class="{!! request()->is('account/payroll/head')?'active':'' !!}  hover">
                            <a href="{{ route('account.payroll.head') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Salary Head
                            </a>

                            <b class="arrow"></b>
                        </li>
                         <?php } ?>

                    </ul>
                </li>
                   <?php } ?>
            </ul>
        </li>
         <?php } ?>

        {{-- Library --}}
         <?php $ret = check_ab($r,$p,'super-admin,library','library,library-index,library-book-issue,library-book-return,library-return-over,library-issue-history,book-index,book-add,book-edit,book-view,book-delete,book-active,book-in-active,book-bulk-action,book-add-copies,book-status,book-bulk-copies-delete,book-category-index,book-category-add,book-category-edit,book-category-delete,book-category-active,book-category-in-active,book-category-bulk-action,library-circulation-index,library-circulation-add,library-circulation-edit,library-circulation-delete,library-circulation-active,library-circulation-in-active,library-circulation-bulk-action,library-member-index,library-member-add,library-member-edit,library-member-delete,library-member-active,library-member-in-active,library-member-bulk-action,library-member-staff,library-member-staff-view,library-member-student,library-member-student-view');if($ret){ ?>
        <li class="{!! request()->is('library*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-book" aria-hidden="true"></i>
                <span class="menu-text">Library</span>

                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
                 <?php $ret = check_ab($r,$p,'super-admin,library','library,library-index,book-index,book-add,book-edit,book-view,book-delete,book-active,book-in-active,book-bulk-action,book-add-copies,book-status,book-bulk-copies-delete,book-category-index,book-category-add,book-category-edit,book-category-delete,book-category-active,book-category-in-active,book-category-bulk-action');if($ret){ ?>
                <li class="{!! request()->is('library/book*')?'active':'' !!} hover">
                    <a href="{{ route('library.book') }}" accesskey="L">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Books
                    </a>

                    <b class="arrow"></b>
                </li>
                  <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,library','library-member-index,library-member-add,library-member-edit,library-member-delete,library-member-active,library-member-in-active,library-member-bulk-action,library-member-staff,library-member-staff-view,library-member-student,library-member-student-view');if($ret){ ?>
                <li class="{!! request()->is('library/member*') || request()->is('library/student*') || request()->is('library/staff*') ?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Members
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,library','library-member-index,library-member-add,library-member-edit,library-member-delete,library-member-active,library-member-in-active,library-member-bulk-action');if($ret){ ?>
                        <li class="{!! request()->is('library/member*')?'active':'' !!} hover">
                            <a href="{{ route('library.member') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Membership
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,library','library-member-student,library-member-student-view');if($ret){ ?>
                        <li class="{!! request()->is('library/student*')?'active':'' !!} hover">
                            <a href="{{ route('library.student') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Student Member
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                           <?php $ret = check_ab($r,$p,'super-admin,library','library-member-staff,library-member-staff-view');if($ret){ ?>
                        <li class="{!! request()->is('library/staff*')?'active':'' !!} hover">
                            <a href="{{ route('library.staff') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Staff Member
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>

                    </ul>
                </li>
                    <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,library','library-issue-history');if($ret){ ?>
                <li class="{!! request()->is('library/issue-history*')?'active':'' !!} hover">
                    <a href="{{ route('library.issue-history') }}">
                        <i class="menu-icon fa fa-history"></i>
                        Issue History
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,library','library-return-over');if($ret){ ?>
                <li class="{!! request()->is('library/return-over*')?'active':'' !!} hover">
                    <a href="{{ route('library.return-over') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Return Period Over
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,library','library-circulation-index,library-circulation-add,library-circulation-edit,library-circulation-delete,library-circulation-active,library-circulation-in-active,library-circulation-bulk-action');if($ret){ ?>
                <li class="{!! request()->is('library/circulation*')?'active':'' !!}  hover">
                    <a href="{{ route('library.circulation') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Circulation Setting
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
            </ul>
        </li>
         <?php } ?>

        {{-- Attendance --}}
         <?php $ret = check_ab($r,$p,'super-admin,account','attendance-master-index,attendance-master-add,attendance-master-edit,attendance-master-delete,attendance-master-active,attendance-master-in-active,attendance-master-bulk-action,student-attendance-index,student-attendance-add,student-attendance-delete,student-attendance-bulk-action,staff-attendance-index,staff-attendance-add,staff-attendance-delete,staff-attendance-bulk-action');if($ret){ ?>
        <li class="{!! request()->is('attendance*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-calendar" aria-hidden="true"></i>
                <span class="menu-text"> Attendance</span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
                   <?php $ret = check_ab($r,$p,'super-admin,account','student-attendance-index,student-attendance-add,student-attendance-delete,student-attendance-bulk-action');if($ret){ ?>
                <li class="{!! request()->is('attendance/student*')?'active':'' !!} hover">
                    <a href="{{ route('attendance.student') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Student Attendance
                    </a>

                    <b class="arrow"></b>
                </li>
                  <?php } ?>

                  <?php $ret = check_ab($r,$p,'super-admin,account','staff-attendance-index,staff-attendance-add,staff-attendance-delete,staff-attendance-bulk-action');if($ret){ ?>
                <li class="{!! request()->is('attendance/staff*')?'active':'' !!} hover">
                    <a href="{{ route('attendance.staff') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Staff Attendance
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','attendance-master-index,attendance-master-add,attendance-master-edit,attendance-master-delete,attendance-master-active,attendance-master-in-active,attendance-master-bulk-action');if($ret){ ?>
                <li class="{!! request()->is('attendance/master*')?'active':'' !!} hover">
                    <a href="{{ route('attendance.master') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Monthly Calendar
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>

            </ul>
        </li>
         <?php } ?>

        {{-- Examination --}}
         <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-index,live-class-schedule,live-class-observe,internal-meeting,mark-assessment-index,gen-report-card-index,exam-mode-setup-index,exam-term-setup-index,exam-type-setup-index,exam-papertype-setup-index,exam-questiontype-setup-index,exam-setup-class- result-index');if($ret){ ?>
         <li class="{!! request()->is('exam*') || request()->is('live_class*') || request()->is('internal_meeting*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-graduation-cap" aria-hidden="true"></i>
                <span class="menu-text"> Academic </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
        <ul class="submenu">
            
              <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-index,mark-assessment-index,gen-report-card-index,exam-mode-setup-index,exam-term-setup-index,exam-type-setup-index,exam-papertype-setup-index,exam-questiontype-setup-index,exam-setup-class- result-index');if($ret){ ?>
            
            <li class="{!! request()->is('exam*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                            Examination
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                <ul class="submenu">
                    
                    <!-- <li class="{!! request()->is('exam/mark-ledger')?'active':'' !!}  hover">
                        <a href="{{ route('exam.mark-ledger') }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Mark Ledger
                        </a>
                        <b class="arrow"></b>
                    </li> -->
                      <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-setup-class- result-index,exam-mode-setup-index,exam-term-setup-index,exam-type-setup-index,exam-papertype-setup-index,exam-questiontype-setup-index');if($ret){ ?>
                    <li class="{!! request()->is('exam/setup*')?'active':'' !!} hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Setup
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                              <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-mode-setup-index');if($ret){ ?>
                            <li class="{!! request()->is('exam/setup/exam-mode*')?'active':'' !!} hover">
                                <a href="{{ route('exam.setup.exam-mode') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Exam Mode
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-term-setup-index');if($ret){ ?>
                             <li class="{!! request()->is('exam/setup/exam-term*')?'active':'' !!} hover">
                                <a href="{{ route('exam.setup.exam-term') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Term
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-type-setup-index');if($ret){ ?>
                            <li class="{!! request()->is('exam/setup/exam-type*')?'active':'' !!} hover">
                                <a href="{{ route('exam.setup.exam-type') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Exam Type
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-papertype-setup-index');if($ret){ ?>
                            <li class="{!! request()->is('exam/setup/exam-paper*')?'active':'' !!} hover">
                                <a href="{{ route('exam.setup.exam-paper') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Exam Paper Type
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-questiontype-setup-index');if($ret){ ?>
                            <li class="{!! request()->is('exam/setup/question-type*')?'active':'' !!} hover">
                                <a href="{{ route('exam.setup.question-type') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Question Type
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-setup-class- result-index');if($ret){ ?>
                            <li class="{!! request()->is('exam/setup/result-type*')?'active':'' !!} hover">
                                <a href="{{ route('exam.setup.result-type') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Class Result Type
                                </a>
                            </li>
                              <?php } ?>
                        </ul>
                    </li>
                      <?php } ?>
                     <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','exam-index');if($ret){ ?>
                     
                     <?php /* 
                    <li class="{!! request()->is('exam/create*')?'active':'' !!} hover">
                        <a href="{{ route('exam.create') }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Create Exam
                        </a>
                        <b class="arrow"></b>
                    </li>
                    */ ?>
                    <li class="{!! request()->is('exam/create*') || request()->is('exam/list*') ?'active':'' !!} hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Exam
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('exam/create*')?'active':'' !!} hover">
                                <a href="{{ route('exam.create') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Create Exam
                                </a>
                            </li>
                             <li class="{!! request()->is('exam/list*')?'active':'' !!} hover">
                                <a href="{{ route('exam.list') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Exam List
                                </a>
                            </li>
                        </ul>
                    </li>
                      <?php } ?>
                     <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','mark-assessment-index');if($ret){ ?>
                    <li class="{!! request()->is('exam/assesment*')?'active':'' !!} hover">
                        <a href="{{ route('exam.assessment') }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Record Mark / Assessment
                        </a>
                        <b class="arrow"></b>
                    </li>
                      <?php } ?>
                       <!-- Student Remark module -->
                       <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','mark-assessment-index');if($ret){ ?>
                    <li class="{!! request()->is('exam/studentRemark*')?'active':'' !!} hover">
                        <a href="{{ route('exam.studentRemark') }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Add Student Remark
                        </a>
                        <b class="arrow"></b>
                    </li>
                      <?php } ?>
                      <!-- Student Remark module -->
                     <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','gen-report-card-index');if($ret){ ?>
                    <li class="{!! request()->is('exam/report*')?'active':'' !!} hover">
                        <a href="{{ route('exam.report') }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Generate Report Card
                        </a>
                        <b class="arrow"></b>
                    </li>
                      <?php } ?>
                      <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','gen-report-card-index');if($ret){ ?>
                    <li class="{!! request()->is('exam/studentMarkExcel*')?'active':'' !!} hover">
                        <a href="{{ route('exam.studentMarkExcel') }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Generate Cumulative Sheet 
                        </a>
                        <b class="arrow"></b>
                    </li>
                      <?php } ?>
                   <!--  <li class="hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Print
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('exam/admit-card*')?'active':'' !!} hover">
                                <a href="{{ route('exam.admit-card') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Admit Card
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('exam/routine*')?'active':'' !!} hover">
                                <a href="{{ route('exam.routine') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Routine
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('exam/mark-sheet*')?'active':'' !!} hover">
                                <a href="{{ route('exam.mark-sheet') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Grade/Mark Sheet
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li> -->
                </ul>
            </li>
              <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','live-class-schedule,live-class-observe');if($ret){ ?>
                <li class="{!! request()->is('live_class*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <span class="menu-text"> Live Class</span>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','live-class-schedule');if($ret){ ?>
                        <li class="{!! request()->is('live_class/schedule*')?'active':'' !!} hover">
                            <a href="{{ route('live_class.schedule') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Schedule Live Class
                            </a>
                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','live-class-schedule');if($ret){ ?>
                        <li class="{!! request()->is('live_class/observe*')?'active':'' !!} hover">
                            <a href="{{ route('live_class.observe_class') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Observe Live Class
                            </a>
                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                    </ul>    
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,Admission,account','internal-meeting');if($ret){ ?>
                <li class="{!! request()->is('internal_meeting*')?'active':'' !!} hover">
                    <a href="{{ route('internal_meeting') }}" >
                        <span class="menu-text"> Internal Meeting</span>
                    </a>
                    <b class="arrow"></b>  
                </li>
                 <?php } ?>
        </ul>
        </li>
         <?php } ?>
        {{-- Hostel --}}
          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','hostel-index,room-type-index,food-index,food-category-index,eating-time-index,hostel-resident-index,hostel-registration,hostel-occupation-history,hostel-collect-fees,food-meal-schedule-index');if($ret){ ?>
        <li class="{!! request()->is('hostel*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-bed" aria-hidden="true"></i>
                <span class="menu-text"> Hostels </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
                  <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','hostel-resident-index,hostel-registration,hostel-occupation-history');if($ret){ ?>
                <li class="{!! request()->is('hostel/resident*')?'active':'' !!} hover">
                    <a href="{{ route('hostel.resident') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Resident
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','hostel-resident-index');if($ret){ ?>
                        <li class="{!! request()->is('hostel/resident')?'active':'' !!} hover">
                            <a href="{{ route('hostel.resident') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Detail
                            </a>
                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','hostel-registration');if($ret){ ?>
                        <li class="{!! request()->is('hostel/resident/add')?'active':'' !!} hover">
                            <a href="{{ route('hostel.resident.add') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Registration
                            </a>
                            <b class="arrow"></b>
                        </li>
                         <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','hostel-occupation-history');if($ret){ ?>
                        <li class="{!! request()->is('hostel/resident/history')?'active':'' !!} hover">
                            <a href="{{ route('hostel.resident.history') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Occupant History
                            </a>
                            <b class="arrow"></b>
                        </li>
                         <?php } ?>

                    </ul>
                </li>
                   <?php } ?>
                   <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','hostel-index,room-type-index');if($ret){ ?>
                <li class="{!! request()->is('hostel*')?'active':'' !!} hover">
                    <a href="{{ route('hostel') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Hostel
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','hostel-index');if($ret){ ?>
                        <li class="{!! request()->is('hostel*')?'active':'' !!} hover">
                            <a href="{{ route('hostel') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Hostel
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','room-type-index');if($ret){ ?>
                        <li class="{!! request()->is('hostel/room-type*')?'active':'' !!} hover">
                            <a href="{{ route('hostel.room-type') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Room Type
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                    </ul>
                </li>
                  <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','food-index,food-category-index,eating-time-index,food-meal-schedule-index');if($ret){ ?>
                <li class="{!! request()->is('hostel/food*')?'active':'' !!} hover">
                    <a href="{{ route('hostel') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Food & Meal
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                         <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','food-meal-schedule-index');if($ret){ ?>
                        <li class="{!! request()->is('hostel/food*')?'active':'' !!} hover">
                            <a href="{{ route('hostel.food') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Meal Schedule
                            </a>

                            <b class="arrow"></b>
                        </li>
                           <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','eating-time-index');if($ret){ ?>
                        <li class="{!! request()->is('hostel/food/eating-time*')?'active':'' !!} hover">
                            <a href="{{ route('hostel.food.eating-time') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Eating Time
                            </a>

                            <b class="arrow"></b>
                        </li>
                           <?php } ?>
                         <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','food-category-index');if($ret){ ?>
                        <li class="{!! request()->is('hostel/food/category*')?'active':'' !!} hover">
                            <a href="{{ route('hostel.food.category') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Food Category
                            </a>

                            <b class="arrow"></b>
                        </li>
                           <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','food-index');if($ret){ ?>
                        <li class="{!! request()->is('hostel/food/item*')?'active':'' !!} hover">
                            <a href="{{ route('hostel.food.item') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Food Item
                            </a>
                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                    </ul>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','hostel-collect-fees');if($ret){ ?>
                <li class="{!! request()->is('hostel/collect/*')?'active':'' !!} hover">
                    <a href="{{ route('hostel.fee') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Collect Fees
                    </a>
                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                 
                 <?php $ret = check_ab($r,$p,'super-admin,admin,Receptionist,account','hostel-leave-index,hostel-leave-edit,hostel-leave-delete');if($ret){ ?>
                    <li class="{!! request()->is('hostel/Leave')?'active':'' !!} hover">
                        <a href="{{ route('hostel.Leave') }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Hostel Leave
                        </a>
                        <b class="arrow"></b>
                    </li>
                 <?php } ?>
            </ul>
        </li>
         <?php } ?>

        {{-- Transport --}}
         <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-user-index,transport-user-add,transport-user-history,vehicle-index,transport-route-index,stoppage-index,transport-collect-fee,transport-collection-report,transport-due-report,vehicle-maintenance-index,vehicle-maintenance-edit,vehicle-maintenance-delete,vehicle-dailyentry-index,vehicle-dailyentry-edit,vehicle-dailyentry-delete');if($ret){ ?>
        <li class="{!! request()->is('transport*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-bus" aria-hidden="true"></i>
                <span class="menu-text"> Transport </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
             <ul class="submenu">
                 <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-user-index,transport-user-add,transport-user-history');if($ret){ ?>
                 <li class="{!! request()->is('transport/user*')?'active':'' !!} hover">
                     <a href="{{ route('transport.user') }}">
                         <i class="menu-icon fa fa-caret-right"></i>
                         Traveller/User
                         <b class="arrow fa fa-angle-r"></b>
                     </a>
                     <b class="arrow"></b>
                     <ul class="submenu">
                           <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-user-index');if($ret){ ?>
                         <li class="{!! request()->is('transport/user')?'active':'' !!} hover">
                             <a href="{{ route('transport.user') }}">
                                 <i class="menu-icon fa fa-caret-right"></i>
                                 Detail
                             </a>
                             <b class="arrow"></b>
                         </li>
                            <?php } ?>
                           <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-user-add');if($ret){ ?>
                         <li class="{!! request()->is('transport/user/add')?'active':'' !!} hover">
                             <a href="{{ route('transport.user.add') }}">
                                 <i class="menu-icon fa fa-caret-right"></i>
                                 Assign Transport
                             </a>
                             <b class="arrow"></b>
                         </li>
                            <?php } ?>
                           <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-user-history');if($ret){ ?>
                         <li class="{!! request()->is('transport/user/history')?'active':'' !!} hover">
                             <a href="{{ route('transport.user.history') }}">
                                 <i class="menu-icon fa fa-caret-right"></i>
                                 User History
                             </a>
                             <b class="arrow"></b>
                         </li>
                         <?php } ?>

                     </ul>
                 </li>
                   <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-route-index');if($ret){ ?>
                 <li class="{!! request()->is('transport/route*')?'active':'' !!} hover">
                     <a href="{{ route('transport.route') }}">
                         <i class="menu-icon fa fa-caret-right"></i>
                         Route
                         <b class="arrow fa fa-angle-r"></b>
                     </a>
                     <b class="arrow"></b>
                 </li>
                  <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','stoppage-index');if($ret){ ?>
                 <li class="{!! request()->is('transport/stoppage*')?'active':'' !!} hover">
                     <a href="{{ route('transport.stoppage') }}">
                         <i class="menu-icon fa fa-caret-right"></i>
                         Stoppage
                         <b class="arrow fa fa-angle-r"></b>
                     </a>
                     <b class="arrow"></b>
                 </li>
                  <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','vehicle-index');if($ret){ ?>
                 <li class="{!! request()->is('transport/vehicle*')?'active':'' !!} hover">
                     <a href="{{ route('transport.vehicle') }}">
                         <i class="menu-icon fa fa-caret-right"></i>
                         Vehicle
                         <b class="arrow fa fa-angle-r"></b>
                     </a>
                     <b class="arrow"></b>
                 </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-collect-fee');if($ret){ ?>
                  <li class="{!! request()->is('transport/collect*')?'active':'' !!} hover">
                     <a href="{{ route('transport.collect') }}">
                         <i class="menu-icon fa fa-caret-right"></i>
                         Collect Fee
                         <b class="arrow fa fa-angle-r"></b>
                     </a>
                     <b class="arrow"></b>
                 </li>
                   <?php } ?>
                   <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-collection-report,transport-due-report');if($ret){ ?>
                 <li class="{!! request()->is('transport/report*')?'active':'' !!} hover">
                     <a href="{{ route('transport.report') }}">
                         <i class="menu-icon fa fa-caret-right"></i>
                         Fee Report
                         <b class="arrow fa fa-angle-r"></b>
                     </a>
                     <b class="arrow"></b>
                     <ul class="submenu">
                         <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-collection-report');if($ret){ ?>
                         <li class="{!! request()->is('transport/report')?'active':'' !!} hover">
                             <a href="{{ route('transport.report') }}">
                                 <i class="menu-icon fa fa-caret-right"></i>
                                 Collection Report
                             </a>
                             <b class="arrow"></b>
                         </li>
                           <?php } ?>
                           <?php $ret = check_ab($r,$p,'super-admin,Receptionist,account','transport-due-report');if($ret){ ?>
                         <li class="{!! request()->is('transport/report')?'active':'' !!} hover">
                             <a href="{{ route('transport.report.due') }}">
                                 <i class="menu-icon fa fa-caret-right"></i>
                                 Due Report
                             </a>
                             <b class="arrow"></b>
                         </li>
                          <?php } ?>


                     </ul>
                 </li>
                 <?php } ?>
                 
                 
                  <?php $ret = check_ab($r,$p,'super-admin,account','vehicle-maintenance-index,vehicle-maintenance-edit,vehicle-maintenance-delete');if($ret){ ?>
                 <li class="{!! request()->is('transport/maintenance*')?'active':'' !!} hover">
                     <a href="{{ route('transport.maintenance') }}">
                         <i class="menu-icon fa fa-caret-right"></i>
                         Vehicle Maintenance
                         <b class="arrow fa fa-angle-r"></b>
                     </a>
                     <b class="arrow"></b>
                 </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','vehicle-dailyentry-index,vehicle-dailyentry-edit,vehicle-dailyentry-delete');if($ret){ ?>
                 <li class="{!! request()->is('transport/DailyEntry*')?'active':'' !!} hover">
                     <a href="{{ route('transport.DailyEntry') }}">
                         <i class="menu-icon fa fa-caret-right"></i>
                         Vehicle Daily Entry
                         <b class="arrow fa fa-angle-r"></b>
                     </a>
                     <b class="arrow"></b>
                 </li>
                 <?php } ?>
                 
             </ul>
        </li>
         <?php } ?>

        {{-- More --}}
         <?php $ret = check_ab($r,$p,'super-admin,account','assignment-index,assignment-add,assignment-edit,assignment-view,assignment-delete,assignment-active,assignment-in-active,assignment-bulk-action,assignment-answer-view,assignment-answer-approve,assignment-answer-reject,assignment-answer-delete,assignment-answer-bulk-action,generate-certificate,manage-certificate-index,download-index,download-add,download-edit,download-delete,download-active,download-in-active,download-bulk-action,career-list');if($ret){ ?>
        <li class="{!! request()->is('assignment') || request()->is('career*')?'active':'' !!} hover ">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-th-list" aria-hidden="true"></i>
                <span class="menu-text"> More </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
             <?php $ret = check_ab($r,$p,'super-admin,account','assignment-index,assignment-add,assignment-edit,assignment-view,assignment-delete,assignment-active,assignment-in-active,assignment-bulk-action,assignment-answer-view,assignment-answer-approve,assignment-answer-reject,assignment-answer-delete,assignment-answer-bulk-action');if($ret){ ?> 
                <li class="{!! request()->is('assignment')?'active':'' !!} hover">
                    <a href="{{ route('assignment') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Assignment
                    </a>
                    <b class="arrow"></b>
                </li> 
                 <?php } ?>
                   <?php $ret = check_ab($r,$p,'super-admin,account','download-index,download-add,download-edit,download-delete,download-active,download-in-active,download-bulk-action');if($ret){ ?>
                <li class="{!! request()->is('download*') ?'active':'' !!} hover">
                    <a href="{{ route('download') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Upload & Download
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                 <?php } ?>

                  <?php $ret = check_ab($r,$p,'super-admin,account','generate-certificate,manage-certificate-index');if($ret){ ?>
                 <li class="{!! request()->is('certificate*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Certificate
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','manage-certificate-index');if($ret){ ?>
                         <li class="{!! request()->is('certificate/manage*')?'active':'' !!} hover">
                            <a href="{{ route('certificate.manage') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                              Manage Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>
                           <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','generate-certificate');if($ret){ ?>
                        <li class="{!! request()->is('certificate/generate')?'active':'' !!} hover">
                            <a href="{{ route('certificate.generate') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Generate Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>
                           <?php } ?>
                    </ul>
                </li>
                 <?php } ?>
                 
                 
                   <?php $ret = check_ab($r,$p,'super-admin,account','career-list');if($ret){ ?>
                <li class="{!! request()->is('career*')?'active':'' !!} hover">
                    <a href="{{ route('career.list') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Career List
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                 <?php } ?>
            </ul>
        </li>
         <?php } ?>

        {{-- Info Center --}}
         <?php $ret = check_ab($r,$p,'super-admin','notice-index,notice-add,notice-edit,notice-delete,sms-email-index,sms-email-delete,sms-email-bulk-action,sms-email-create,sms-email-send,sms-email-staff-send,sms-email-individual-send,sms-email-fee-receipt,sms-email-due-reminder,sms-email-book-return-reminder,sms-email-student-guardian-send');if($ret){ ?>
        <li class="{!! request()->is('info*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-bullhorn" aria-hidden="true"></i>
                <span class="menu-text"> Alert </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
                 <?php $ret = check_ab($r,$p,'super-admin,account','notice-index');if($ret){ ?>
                <li class="{!! request()->is('info/notice*')?'active':'' !!} hover">
                    <a href="{{ route('info.notice') }}" accesskey="L">
                        <i class="menu-icon fa fa-caret-right"></i>
                        User Notice
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','sms-email-index,sms-email-delete,sms-email-bulk-action,sms-email-create,sms-email-send,sms-email-staff-send,sms-email-individual-send,sms-email-fee-receipt,sms-email-due-reminder,sms-email-book-return-reminder,sms-email-student-guardian-send');if($ret){ ?>
                <li class="{!! request()->is('info/smsemail*')?'active':'' !!} hover">
                    <a href="{{ route('info.smsemail') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        SMS / E-mail
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>

            </ul>
        </li>
         <?php } ?>

        <?php $ret = check_ab($r,$p,'super-admin,account','lesson-plan,econtent');if($ret){ ?>
        <li class="{!! request()->is('Lms*') || request()->is('chapter_master*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-file" aria-hidden="true"></i>
                <span class="menu-text"> Lms </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
                <?php $ret = check_ab($r,$p,'super-admin,account','lesson-plan');if($ret){ ?>
                <li class="{!! request()->is('Lms/Lesson_plans')?'active':'' !!}  hover">
                    <a href="{{ route('Lms.Lesson_plans') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Lesson Plans
                    </a>

                    <b class="arrow"></b>
                </li>
                <?php } ?>
                <?php $ret = check_ab($r,$p,'super-admin,account','econtent');if($ret){ ?>
                <li class="{!! request()->is('Lms/E-content/*')?'active':'' !!} hover">
                    <a href="{{ route('Lms.Econtent') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        E-content
                    </a>
                    <b class="arrow"></b>
                </li>
                <?php } ?>
                <?php $ret = check_ab($r,$p,'super-admin,account','lesson-plan');if($ret){ ?>
                <li class="{!! request()->is('chapter_master*')?'active':'' !!} hover">
                    <a href="{{ route('chapter_master') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Chapter Master
                    </a>
                    <b class="arrow"></b>
                </li>
                <?php } ?>
                <?php $ret = check_ab($r,$p,'super-admin,account','lesson-plan');if($ret){ ?>
                <li class="{!! request()->is('Lms/micro_planner')?'active':'' !!}  hover">
                    <a href="{{ route('Lms.micro_planner') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Micro Planner
                    </a>

                    <b class="arrow"></b>
                </li>
                <?php } ?>
                <?php $ret = check_ab($r,$p,'super-admin,account','lesson-plan');if($ret){ ?>
                <li class="{!! request()->is('Lms/macro_planner')?'active':'' !!}  hover">
                    <a href="{{ route('Lms.macro_planner') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Macro Planner
                    </a>

                    <b class="arrow"></b>
                </li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
         <?php $ret = check_ab($r,$p,'super-admin,account','timetable,day-wise-timetable,weekly-timetable');if($ret){ ?>
        <li class="hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-clock-o" aria-hidden="true"></i>
                <span class="menu-text"> Timetable </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>

            <ul class="submenu">
                  <?php $ret = check_ab($r,$p,'super-admin,account','day-wise-timetable');if($ret){ ?>
                <li class="{!! request()->is('timetable/daily*')?'active':'' !!} hover">
                    <a href="{{ route('timetable.daily') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Day-wise Time Table
                    </a>

                    <b class="arrow"></b>
                </li>
                  <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','weekly-timetable');if($ret){ ?>
                <li class="{!! request()->is('timetable')?'active':'' !!} hover">
                    <a href="{{ route('timetable') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Weekly Time Table
                    </a>

                    <b class="arrow"></b>
                </li>
                  <?php } ?>
               
            </ul>
        </li>        
         <?php } ?>
        
         <?php $ret = check_ab($r,$p,'super-admin,account','inventory,inventory-setup,supplier-index,product-index,purchase-index,inventory-brand-index,inventory-unit-index,inventory-category-index,inventory-gst-index,inventory-labels-index,inventory-purchase-status-index');if($ret){ ?>
        <li class="{!! request()->is('Inventory*')?'active open':''!!} hover">
           <a href="#" class="dropdown-toggle">
               <i class="menu-icon fa fa-suitcase"></i>
               <span>Inventory</span>
               <b class="arrow fa fa-angle-down"></b>
           </a> 
           <b class="arrow"></b>
           <ul class="submenu">
                
             <?php $ret = check_ab($r,$p,'super-admin,account','supplier-index');if($ret){ ?>
            <li class="{!!request()->is('Inventory/supplier*')?'active':''!!} hover">
                <a href="{{route('inventory.supplier')}}">
                    <i class="menu-icon fa fa-caret-right"></i>
                   Suppliers
                </a>
            </li>
              <?php } ?>
              <?php $ret = check_ab($r,$p,'super-admin,account','product-index');if($ret){ ?>
            <li class="{!!request()->is('Inventory/product*')?'active':''!!} hover">
                <a href="{{route('inventory.product')}}">
                    <i class="menu-icon fa fa-caret-right"></i>
                   Products
                </a>
            </li>
              <?php } ?>
              <?php $ret = check_ab($r,$p,'super-admin,account','purchase-index');if($ret){ ?>
            <li class="{!!request()->is('Inventory/purchase*')?'active':''!!} hover">
                <a href="{{route('inventory.purchase')}}">
                    <i class="menu-icon fa fa-caret-right"></i>
                   Purchase
                </a>
            </li>
              <?php } ?>
              <?php $ret = check_ab($r,$p,'super-admin,account','inventory-setup');if($ret){ ?>
            <li class="{!! request()->is('Inventory/brand*') || request()->is('Inventory/units*')||request()->is('Inventory/category*')||request()->is('Inventory/gst*')||request()->is('Inventory/label*')||request()->is('Inventory/PurchaseStatus*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Setup
                    </a>
                    <ul class="submenu">
                         
                        <li class=" {!! request()->is('Inventory/brand*')?'active':'' !!} hover">
                            <a href="{{route('inventory.brand')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Brands
                            </a>
                            <b class="arrow"></b>
                        </li>
                         
                        <li class="{!!request()->is('Inventory/units*')?'active':'' !!} hover">
                            <a href="{{route('inventory.units')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Units
                            </a>
                        </li>
                         
                        <li class="{!!request()->is('Inventory/category*')?'active':''!!} hover">
                            <a href="{{route('inventory.category')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Categories
                            </a>
                        </li>
                          
                        <li class="{!!request()->is('Inventory/gst*')?'active':'' !!} hover">
                            <a href="{{route('inventory.gst')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                GST
                            </a>
                        </li>
                         
                        <li class="{!!request()->is('Inventory/label*')?'active':''!!} hover">
                            <a href="{{route('inventory.label')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Labels
                            </a>
                        </li>
                         
                        <li class="{!!request()->is('Inventory/purchaseStatus*')?'active':''!!} hover">
                            <a href="{{route('inventory.purchaseStatus')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Purchase Status
                            </a>
                        </li>
                          
                    </ul>
                </li>
                   <?php } ?>
           </ul>
        </li>
         <?php } ?>

         <?php $ret = check_ab($r,$p,'super-admin,account','branch-master-index,is-course-batch-master-index,payment-type-master-index,fine-settings,teacher-co-ordinator-master-index,subject-master-index,assign-subject-classcourse-master-index,assign-subject-teacher-master-index,course-type-master-index,faculty-master-index,section-master-index,batch-master-index,grading-master-index,grading-course-subject-master-index,student-status-master-index,attendance-status-master-index,book-status-master-index,hostel-bed-status-master-index,year-master-index,month-master-index,day-master-index,complaint-type-master-index,source-master-index,reference-master-index,religion-master-index,handicap-master-index,status-master');if($ret){ ?>
        <li class="hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-graduation-cap" aria-hidden="true"></i>
                <span class="menu-text"> Master Setup </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                  <?php $ret = check_ab($r,$p,'super-admin,account','branch-master-index,is-course-batch-master-index');if($ret){ ?>
                <li class="{!! request()->is('branch*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                         Branch
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','branch-master-index');if($ret){ ?>
                        <li class="{!! request()->is('branch')?'active':'' !!} hover">
                            <a href="{{ route('branch') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Branches
                            </a>
                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','is-course-batch-master-index');if($ret){ ?>
                        <li class="{!! request()->is('branch_batchwise')?'active':'' !!} hover">
                            <a href="{{ route('branch_batchwise') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Is Course Batch Enabled
                            </a>
                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                    </ul>
                </li>
                   <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','payment-type-master-index');if($ret){ ?>             
                <li class="{!! request()->is('payment_type*')?'active':'' !!} hover">
                    <a href="{{ route('payment_type') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Payment Type
                    </a>
                    <b class="arrow"></b>
                </li>
                  <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','fine-settings');if($ret){ ?>         
                  <li class="{!! request()->is('EnableFine*')?'active':'' !!} hover">
                      <a href="{{ route('enableFine') }}">
                          <i class="menu-icon fa fa-caret-right"></i>
                          Fine Settings
                      </a>
                      <b class="arrow"></b>
                  </li>
                   <?php } ?>
                <!-- teacher/cordinator master -->
                  <?php $ret = check_ab($r,$p,'super-admin,account','teacher-co-ordinator-master-index');if($ret){ ?>    
                 <li class="{!! request()->is('add_teacher_cordinator*')?'active':'' !!} hover">
                    <a href="{{ route('add_teacher_cordinator') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                          Teacher/Co-Ordinator Master
                    </a>
                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                <!-- teacher/cordinator master -->

                
                <!-- subject master code -->
                   <?php $ret = check_ab($r,$p,'super-admin,account','subject-master-index,assign-subject-classcourse-master-index,assign-subject-teacher-master-index');if($ret){ ?> 
                 <li class="{!! request()->is('addsubject*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                         Subject 
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','subject-master-index');if($ret){ ?> 
                        <li class="{!! request()->is('addsubject')?'active':'' !!} hover">
                            <a href="{{ route('addsubject') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Subject Master
                            </a>
                            <b class="arrow"></b>
                        </li>
                         <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','assign-subject-classcourse-master-index');if($ret){ ?> 
                         <li class="{!! request()->is('timetable/subject*')?'active':'' !!} hover">
                            <a href="{{ route('timetable.subject') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Assign Subject To {{ env('course_label') }}
                            </a>

                       <b class="arrow"></b>
                      </li>
                         <?php } ?>
                        <?php $ret = check_ab($r,$p,'super-admin,account','assign-subject-teacher-master-index');if($ret){ ?> 
                        <li class="{!! request()->is('timetable/assignsubject*')?'active':'' !!} hover">
                            <a href="{{ route('timetable.assign') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Assign Subject To Teacher
                            </a>

                            <b class="arrow"></b>
                        </li>
                           <?php } ?>
                    </ul>
                </li> 
                  <?php } ?>
                 <!-- subject master code -->
                 <?php $ret = check_ab($r,$p,'super-admin,account','course-type-master-index,faculty-master-index,section-master-index,batch-master-index');if($ret){ ?>
                <li class="{!! request()->is('faculty*') || request()->is('semester*') || request()->is('courseType*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Academic Level
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','course-type-master-index');if($ret){ ?>
                        <li class="{!! request()->is('courseType*')?'active':'' !!} hover">
                            <a href="{{ route('courseType') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               {{ env('course_label') }} Type
                            </a>
                            <b class="arrow"></b>
                        </li>
                         <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','faculty-master-index');if($ret){ ?>
                        <li class="{!! request()->is('faculty*')?'active':'' !!} hover">
                            <a href="{{ route('faculty') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Faculty/Level/{{ env('course_label') }}
                            </a>
                            <b class="arrow"></b>
                        </li>
                         <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','section-master-index');if($ret){ ?>
                        <li class="{!! request()->is('semester*')?'active':'' !!} hover">
                            <a href="{{ route('semester') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Section
                            </a>
                            <b class="arrow"></b>
                        </li>
                         <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','batch-master-index');if($ret){ ?>
                        <li class="{!! request()->is('courseBatch*')?'active':'' !!} hover">
                            <a href="{{ route('courseBatch') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               {{ env('course_label') }} Batch
                            </a>
                            <b class="arrow"></b>
                        </li>
                         <?php } ?>
                    </ul>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','grading-master-index,grading-course-subject-master-index');if($ret){ ?>
                <li class="{!! request()->is('grading*') || request()->is('subject*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Grading/Subject
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','grading-master-index');if($ret){ ?>
                        <li class="{!! request()->is('grading*')?'active':'' !!} hover">
                            <a href="{{ route('grading') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Grading
                            </a>
                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                           <?php $ret = check_ab($r,$p,'super-admin,account','grading-course-subject-master-index');if($ret){ ?>
                        <li class="{!! request()->is('subject*')?'active':'' !!} hover">
                            <a href="{{ route('subject') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               {{ env('course_label') }} / Subject
                            </a>
                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                    </ul>
                </li>
                  <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','student-status-master-index,attendance-status-master-index,book-status-master-index,hostel-bed-status-master-index');if($ret){ ?>
                <li class="{!! request()->is('*status')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Status Setting
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','student-status-master-index');if($ret){ ?>
                        <li class="{!! request()->is('student-status*')?'active':'' !!} hover">
                            <a href="{{ route('student-status') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Student Status
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                           <?php $ret = check_ab($r,$p,'super-admin,account','attendance-status-master-index');if($ret){ ?>
                        <li class="{!! request()->is('attendance-status*')?'active':'' !!} hover">
                            <a href="{{ route('attendance-status') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Attendance Status
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','book-status-master-index');if($ret){ ?>
                        <li class="{!! request()->is('book-status*')?'active':'' !!} hover">
                            <a href="{{ route('book-status') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Books Status
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','hostel-bed-status-master-index');if($ret){ ?>
                        <li class="{!! request()->is('bed-status*')?'active':'' !!} hover">
                            <a href="{{ route('bed-status') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Hostel Bed Status
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                    </ul>
                </li>
                  <?php } ?>
                   <?php $ret = check_ab($r,$p,'super-admin,account','year-master-index,month-master-index,day-master-index');if($ret){ ?>
                <li class="{!! request()->is('year*') || request()->is('month*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Year & Month
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                          <?php $ret = check_ab($r,$p,'super-admin,account','year-master-index');if($ret){ ?>
                        <li class="{!! request()->is('year*')?'active':'' !!} hover">
                            <a href="{{ route('year') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Year
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                         <?php $ret = check_ab($r,$p,'super-admin,account','month-master-index');if($ret){ ?>
                        <li class="{!! request()->is('month*')?'active':'' !!} hover">
                            <a href="{{ route('month') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Month
                            </a>

                            <b class="arrow"></b>
                        </li>
                          <?php } ?>
                          <?php $ret = check_ab($r,$p,'super-admin,account','day-master-index');if($ret){ ?>
                        <li class="{!! request()->is('day*')?'active':'' !!} hover">
                            <a href="{{ route('day') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Day
                            </a>

                            <b class="arrow"></b>
                        </li>
                         <?php } ?>
                    </ul>
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','complaint-type-master-index');if($ret){ ?>
                <li class="{!! request()->is('complainType*')?'active':'' !!} hover">
                    <a href="{{ route('complainType') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                      Complain Type
                    </a>
                </li>
                  <?php } ?> 
                 <?php $ret = check_ab($r,$p,'super-admin,account','source-master-index');if($ret){ ?> 
                <li class="{!! request()->is('source*')?'active':'' !!} hover">
                    <a href="{{ route('source') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Sources
                    </a>
                </li>
                  <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','reference-master-index');if($ret){ ?>
                <li class="{!! request()->is('reference*')?'active':'' !!} hover">
                    <a href="{{ route('reference') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Reference
                    </a>
                </li>
                  <?php } ?>
                <!-- <li class="{!! request()->is('feeStructure*')?'active':'' !!} hover">
                    <a href="{{ route('feeStructure') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Fee Structure
                    </a>
                </li> -->
                 <?php $ret = check_ab($r,$p,'super-admin,account','religion-master-index');if($ret){ ?>
                <li class="{!! request()->is('religion*')?'active':'' !!} hover">
                    <a href="{{ route('religion') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Religion
                    </a>

                    <b class="arrow"></b>
                </li>
                  <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','status-master');if($ret){ ?>
                <li class="{!! request()->is('status*')?'active':'' !!} hover">
                    <a href="{{ route('status') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Status
                    </a>

                    <b class="arrow"></b>
                </li>
                  <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','handicap-master-index');if($ret){ ?>
                <li class="{!! request()->is('handicap*')?'active':'' !!} hover">
                    <a href="{{ route('handicap') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Handicap
                    </a>
                    <b class="arrow"></b>
                </li>
                  <?php } ?>      
            </ul>
        </li>
         <?php } ?>
         <?php $ret = check_ab($r,$p,'super-admin,account','student-detail,staff-index,payroll-balance,library-return-over,fee-reports,due-report,report-head-collection-due,report-payment-type,report-month-wise,report-no-due-headwise-cumulative,report-student-headwise,report-student-monthwise, no-due-month-wise');if($ret){ ?>
        <li class="{!! request()->is('report*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-bar-chart"  aria-hidden="true"></i>
                <span class="menu-text"> Report</span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                 <?php $ret = check_ab($r,$p,'super-admin,account','student-detail');if($ret){ ?>
                <li class="{!! request()->is('report/student*')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Student

                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="{!! request()->is('report/student*')?'active':'' !!} hover">
                            <a href="{{ route('report.student') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                               Student Detail
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="{!! request()->is('student*')?'active':'' !!} hover">
                            <a href="{{ route('student') }}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Student List
                            </a>

                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
                 <?php } ?>
                 <?php $ret = check_ab($r,$p,'super-admin,account','staff-index');if($ret){ ?>
                <li class="{!! request()->is('staff*')?'active':'' !!} hover">
                    <a href="{{ route('staff') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Staff
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                <?php $ret = check_ab($r,$p,'super-admin,account','fee-reports,due-report,report-head-collection-due,report-payment-type,report-month-wise,report-no-due-headwise-cumulative,report-student-headwise,report-student-monthwise, no-due-month-wise');if($ret){ ?>
                <li class="{!! request()->is('account/fees/balance')?'active':'' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                       Fees
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                            <?php $ret = check_ab($r,$p,'super-admin,account','due-report');if($ret){ ?>
                            <li class="{!! request()->is('download*')?'active':'' !!} hover">
                                <a href="{{ route('due_report') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Due Report
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-head-collection-due');if($ret){ ?>
                            <li class="{!! request()->is('headwiseTotalReport*')?'active':'' !!} hover">
                                <a href="{{ route('headwiseTotalReport') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Fee Head(Collection / Due)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-payment-type');if($ret){ ?>
                            <li class="{!! request()->is('feeReport*')?'active':'' !!} hover">
                                <a href="{{ route('fee_report') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Fee Report (Payment Type)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-month-wise');if($ret){ ?>
                            <li class="{!! request()->is('feeReportMonthWise*')?'active':'' !!} hover">
                                <a href="{{ route('fee_report_month_wise') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Fee Report (Monthwise)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-no-due-headwise-cumulative');if($ret){ ?>
                            <li class="{!! request()->is('noDues*')?'active':'' !!} hover">
                                <a href="{{ route('noDues') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    No-Due (Headwise Cumulative)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                              <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account',' no-due-month-wise');if($ret){ ?>
                            <li class="{!! request()->is('noDuesMonthWise*')?'active':'' !!} hover">
                                <a href="{{ route('noDuesMonthWise') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    No-Due (Monthwise)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                               <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-student-headwise');if($ret){ ?>
                            <li class="{!! request()->is('noDUes_student*')?'active':'' !!} hover">
                                <a href="{{ route('noDues_student') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                   Student (Headwise)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                               <?php } ?>
                              <?php $ret = check_ab($r,$p,'super-admin,account','report-student-monthwise');if($ret){ ?>
                            <li class="{!! request()->is('noDues_studentMonthWise*')?'active':'' !!} hover">
                                <a href="{{ route('noDues_studentMonthWise') }}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Student (MonthWise)
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                            </li>
                               <?php } ?>
                    </ul>
                </li>
                  <?php } ?>
                   <?php $ret = check_ab($r,$p,'super-admin,account','payroll-balance');if($ret){ ?>
                <li class="{!! request()->is('account/payroll/balance')?'active':'' !!} hover">
                    <a href="{{ route('account.payroll.balance') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Due Salary
                    </a>
                    <b class="arrow"></b>
                </li>
                  <?php } ?>
                   <?php $ret = check_ab($r,$p,'super-admin,account,library','library-return-over');if($ret){ ?>
                <li class="{!! request()->is('library/return-over')?'active':'' !!} hover">
                    <a href="{{ route('library.return-over') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Library Return Period Over
                    </a>
                    <b class="arrow"></b>
                </li>
                  <?php } ?>
            </ul>
        </li>
         <?php } ?>
        {{-- Account --}}
         <?php $ret = check_ab($r,$p,'super-admin,account','miscellaneous,fee-head-index,assign-fee-index,collect-fee-index,mis-collection-list,mis-cancel-receipt');if($ret){ ?>
        <li class="{!! request()->is('miscellaneous*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon  fa fa-inr" aria-hidden="true"></i>
                <span class="menu-text"> Miscellaneous </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
                <?php $ret = check_ab($r,$p,'super-admin,account','fee-head-index');if($ret){ ?>
                <li class="{!! request()->is('miscellaneous/head')?'active':'' !!}  hover">
                    <a href="{{ route('miscellaneous.head') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Fees Head
                    </a>

                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','assign-fee-index');if($ret){ ?>
                <li class="{!! request()->is('miscellaneous/newAssignList*')?'active':'' !!} hover">
                    <a href="{{ route('miscellaneous.newAssignList') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Assign Fees
                    </a>
                    <b class="arrow"></b>
                </li>
                 <?php } ?>
                 
                 
                  <?php $ret = check_ab($r,$p,'super-admin,account','collect-fee-index');if($ret){ ?>
                <li class="{!! request()->is('miscellaneous/collect_fee*')?'active':'' !!} hover">
                    <a href="{{ route('miscellaneous.collect_fee') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Collect Fees
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                
                </li>
                   <?php } ?>
                  <?php $ret = check_ab($r,$p,'super-admin,account','mis-collection-list');if($ret){ ?>
                <li class="{!! request()->is('miscellaneous/collection_List*')?'active':'' !!} hover">
                    <a href="{{ route('miscellaneous.collection_List') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Collection List
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                 <?php } ?>
               
                  <?php $ret = check_ab($r,$p,'super-admin,account','mis-cancel-receipt');if($ret){ ?>
                <li class="{!! request()->is('miscellaneous/cancelled_receipts*')?'active':'' !!} hover">
                    <a href="{{ route('miscellaneous.cancelled_receipts') }}">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Cancelled Receipts
                        <b class="arrow fa fa-angle-r"></b>
                    </a>
                </li>
                 <?php } ?>
                      
            </ul>
        </li>
         <?php } ?>
        
    </ul><!-- /.nav-list -->
</div>
