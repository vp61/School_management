@extends('user-student.layouts.master')

@section('css')


@endsection

@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        Exams
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                             Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        @include('includes.flash_messages')
                        @include($view_path.'.exam.includes.table')
                    </div>
                </div><!-- /.row -->

            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->



@endsection

@section('js')
    <!-- page specific plugin scripts -->
    @include('includes.scripts.dataTable_scripts')
   <?php /* <script>
        var date = new Date();

        var countDownDate = new Date("Jul 03, 2020 15:08:30").getTime();
        
        // Update the count down every 1 second
        var x = setInterval(function() {
        console.log(new Date());
          // Get today's date and time
          var now = new Date().getTime();

          // Find the distance between now and the count down date
          var distance = countDownDate - now;

          // Time calculations for days, hours, minutes and seconds
          var days = Math.floor(distance / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);

          // Display the result in the element with id="demo"
          document.getElementById("demo").innerHTML = days + "d " + hours + "h "
          + minutes + "m " + seconds + "s ";

          // If the count down is finished, write some text
          if (distance < 0) {
            clearInterval(x);
            alert('Time Over - Please submit your anser sheet by clicking on submit button now.');
            document.getElementById("demo").innerHTML = "EXPIRED";
          }
        }, 1000);

    </script> */?>
@endsection