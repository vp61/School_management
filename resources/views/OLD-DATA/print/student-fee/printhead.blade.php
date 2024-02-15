            <div class="col-8">
                <h1 class="text-center">{{ $data['branch'][0]->branch_name}}</h1>
                <h5 class="text-center">{{ $data['branch'][0]->branch_address}}</h5>
                <h5 class="text-center">{{ $data['branch'][0]->branch_mobile}}</h5>
                <div>
                   <p>.</p> 
                </div>

                 @if(!empty($data['branch'][0]->branch_logo))
                <div class="text-left logo" >
                <img src="{{asset('images/logo/'.$data['branch'][0]->branch_logo)}}" style="width: 165px; margin: -239px 0px -49px 4px;">
                </div>
                @endif

                <div class="text-right" style="margin: -135px 18px 0px 0px;">
                    <div >
                                            <a href="#" onclick="window.print()">
                                                <i class="ace-icon fa fa-print bigger-180"></i>
                                            </a>
                                        </div>
                <h5 class="widget-title grey lighter no-margin-bottom">
                Master Invoice - #M{{$data['fee_master']->id}}
                </h5>
                <div class="widget-toolbar no-border invoice-info">
                                            <span class="invoice-info-label">User:</span>
                                            <span class="red">{{isset(auth()->user()->name)?auth()->user()->name:""}}</span>
                                           

                                            <br />
                                            <span class="invoice-info-label">Date:</span>
                                            <span class="blue">{{ \Carbon\Carbon::parse(now())->format('Y-m-d')}}</span>
                                        </div>
                </div>
                </div>

           
                 </div>

                                    <div class="widget-body">
                                        <div class="widget-main padding-0">
                                            <div class="print-info">
                                                <table class="table  no-border">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <span>Reg No. : </span>{{ $data['student']->reg_no }}
                                                                <hr class="hr-2 no-border">
                                                                <span>Name : </span><strong>{{ $data['student']->first_name.' '.$data['student']->middle_name.' '.$data['student']->last_name }}</strong>
                                                                <hr class="hr-2 no-border">
                                                                <span>Course: </span>{{ ViewHelper::getFacultyTitle($data['student']->faculty) }}
                                                                <!-- <hr class="hr-2 no-border">
                                                                {{ ViewHelper::getSemesterTitle($data['student']->semester) }}
 -->

                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
