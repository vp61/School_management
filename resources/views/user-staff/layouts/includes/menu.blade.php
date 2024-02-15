
<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse ace-save-state hidden-print">
    <script type="text/javascript">
        try{ace.settings.loadState('sidebar')}catch(e){}
    </script>

    <ul class="nav nav-list">
        {{-- Dashboard --}}
        <li class="{!! request()->is('user-staff')?'active':'' !!}">
            <a href="{{ route('user-staff') }}" >
                <i class="menu-icon fa fa-tachometer"></i>
                <span class="menu-text"> Dashboard </span>
            </a>
        </li>

        {{-- Account --}}
        <li class="{!! request()->is('user-staff/payroll*')?'active open':'' !!}  hover">
            <a href="{{ route('user-staff.payroll') }}" >
                <i class="menu-icon fa fa-calculator" aria-hidden="true"></i>
                <span class="menu-text">Payroll</span>
            </a>
        </li>

        {{-- Library --}}
        <li class="{!! request()->is('user-staff/library*')?'active':'' !!} hover">
            <a href="{{ route('user-staff.library') }}" >
                <i class="menu-icon fa fa-book" aria-hidden="true"></i>
                <span class="menu-text">Library</span>
            </a>
            <b class="arrow"></b>
        </li>

        {{-- Attendence --}}
        <li class="{!! request()->is('user-staff/attendance*')?'active':'' !!} hover">
            <a href="{{ route('user-staff.attendance') }}">
                <i class="menu-icon fa fa-calendar" aria-hidden="true"></i>
                <span class="menu-text"> Attendance</span>
            </a>
            <b class="arrow"></b>
        </li>

        {{-- Hostel --}}
        <li class="{!! request()->is('user-staff/hostel*')?'active':'' !!} hover">
            <a href="{{ route('user-staff.hostel') }}">
                <i class="menu-icon  fa fa-bed" aria-hidden="true"></i>
                <span class="menu-text"> Hostels </span>
            </a>
            <b class="arrow"></b>
        </li>

        {{-- Transport --}}
        <li class="{!! request()->is('user-staff/transport*')?'active':'' !!} hover">
            <a href="{{ route('user-staff.transport') }}">
                <i class="menu-icon  fa fa-bus" aria-hidden="true"></i>
                <span class="menu-text"> Transport </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li class="{!! request()->is('user-staff/subject*')?'active':'' !!} hover">
            <a href="{{ route('user-staff.subject') }}">
                <i class="menu-icon  fa fa-list-alt" aria-hidden="true"></i>
                <span class="menu-text"> {{ env('course_label') }} </span>
            </a>
            <b class="arrow"></b>
        </li>

        {{-- Notice --}}
        <li class="{!! request()->is('user-staff/notice*')?'active':'' !!} hover">
            <a href="{{ route('user-staff.notice') }}">
                <i class="menu-icon  fa fa-bullhorn" aria-hidden="true"></i>
                <span class="menu-text"> Notice </span>
            </a>
            <b class="arrow"></b>
        </li>

        {{-- Assignment --}}
        <li class="{!! request()->is('user-staff/assignment*')?'active':'' !!} hover">
            <a href="{{ route('user-staff.assignment') }}">
                <i class="menu-icon  fa fa-tasks" aria-hidden="true"></i>
                <span class="menu-text"> Assignment </span>
            </a>
            <b class="arrow"></b>
        </li>
         {{-- Time Table --}}
        <li class="{!! request()->is('user-staff/timetable*')?'active':'' !!} hover">
            <a href="{{ route('user-staff.timetable') }}">
                <i class="menu-icon  fa fa-clock-o" aria-hidden="true"></i>
                <span class="menu-text"> Time Table </span>
            </a>
            <b class="arrow"></b>
        </li>
         {{-- Live Class --}}
        <li class="{!! request()->is('user-staff/live_class*')?'active':'' !!} hover">
            <a href="{{ route('user-staff.live_class') }}">
                <i class="menu-icon  fa fa-sitemap" aria-hidden="true"></i>
                <span class="menu-text"> Live Class </span>
            </a>
            <b class="arrow"></b>
        </li>
    </ul><!-- /.nav-list -->
</div>
