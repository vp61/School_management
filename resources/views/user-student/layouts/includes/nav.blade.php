<div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="{{ route('user-student') }}" class="navbar-brand">
                <small class="text-capitalize">
                    <i class="fa fa-university" aria-hidden="true"></i>
                    @if(isset($generalSetting->institute))
                        {{$generalSetting->institute}} 
                    @else
                        ©ASHA EDUCATION GROUP - Admin Mitra
                    @endif
                </small>
            </a>

            <button class="pull-right navbar-toggle navbar-toggle-img collapsed" type="button" data-toggle="collapse" data-target=".navbar-buttons,.navbar-menu">
                <span class="sr-only">Toggle user menu</span>

                <img src="{{ asset('assets/images/avatars/user.jpg') }}" alt="Jason's Photo" />
            </button>

            <button class="pull-right navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#sidebar">
                <span class="sr-only">Toggle sidebar</span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>
            </button>
        </div>

        <div class="navbar-buttons navbar-header pull-right  collapse navbar-collapse" role="navigation">
            <ul class="nav ace-nav">
                <li class="light-blue dropdown-modal user-min">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="{{ asset('assets/images/avatars/user.jpg') }}" alt="Jason's Photo" />
                        <span class="user-info">
                                    <small>Welcome,</small>
                                    {{isset(auth()->user()->name)?auth()->user()->name:""}}
                                </span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="{{ route('user-student.profile') }}">
                                <i class="ace-icon fa fa-user"></i>
                                Profile
                            </a>
                        </li>

                        <li class="divider"></li>

                        <li>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ace-icon fa fa-power-off"></i>
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <?php

        $id = auth()->user()->hook_id;
        $session_arr= App\StudentPromotion::select('student_detail_sessionwise.session_id','session.session_name')
        ->leftJoin('session','session.id', '=', 'student_detail_sessionwise.session_id')
        ->where('student_id','=', $id)
        ->get()->toArray(); 
        //dd($session_arr);
        ?>

        
         
        <div class="navbar-buttons navbar-header pull-left  collapse navbar-collapse" role="navigation">
            <div class="collapse navbar-collapse js-navbar-collapse col-md-12">
       
                <ul class="nav navbar-nav">
             
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php
                        if(Session::has('activeSession')){
                            $ssn_active = Session::get('activeSession');
                        }else{ $ssn_active = ""; }
                    ?>
                    {{ ViewHelper::get_session_name($ssn_active) }}
                            &nbsp;
                            <i class="ace-icon fa fa-angle-down bigger-110"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-light-blue dropdown-caret">
                            @foreach ($session_arr as $session_indiv)
                            <li>
                                <a href="{{ route('.switchssn',$session_indiv['session_id'])}}">
                                    <i class="ace-icon fa fa-user bigger-110 blue"></i>
                                    {{$session_indiv['session_name']}}
                                </a>
                            </li>

                            @endforeach
                        </ul>
                    </li>
              
            </ul>
            </div>
      
        </div>

         @if(Session::has('branchmsg'))
               
                <a class="close" data-dismiss="alert">×</a>
                <strong class="alert alert-info">{!!Session::get('branchmsg')!!} </strong> 

         @endif
        

        <nav role="navigation" class="navbar-menu pull-right collapse navbar-collapse">
            <ul class="nav navbar-nav">
                @ability('super-admin', 'admin-control')
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            Admin Control
                            &nbsp;
                            <i class="ace-icon fa fa-angle-down bigger-110"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-light-blue dropdown-caret">
                            <li>
                                <a href="{{ route('user') }}">
                                    <i class="ace-icon fa fa-user bigger-110 blue"></i>
                                    Users & Roles
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('setting.general') }}">
                                    <i class="ace-icon fa fa-cogs bigger-110 blue"></i>
                                    Settings
                                </a>
                            </li>
                        </ul>
                    </li>
                @endability
            </ul>
        </nav>
    </div><!-- /.navbar-container -->
</div>