<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>how to create dynamic barchart in laravel - websolutionstuff.com</title>	
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link href="{{asset('assets/css/components.min.css')}}" rel="stylesheet" type="text/css">	
	<script type="text/javascript" src="{{asset('assets/js/jquery.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>	
	<script type="text/javascript" src="{{asset('assets/js/echarts.min.js')}}"></script> -->
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.googleapis.com.css') }}" />

    <!-- ace styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/ace.min.css') }}" class="ace-main-stylesheet" id="main-ace-style" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{ asset('assets/css/ace-part2.min.css') }}" class="ace-main-stylesheet" />
    <![endif]-->
    <link rel="stylesheet" href="{{ asset('assets/css/ace-skins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/ace-rtl.min.css') }}" />
    <script src="{{ asset('assets/js/ace-extra.min.js') }}"></script>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" charset="utf-8"></script>
</head>
<body>
<div class="col-md-12">
	<h1 class="text-center">how to create dynamic barchart in</h1>
    <div class="col-md-8 col-md-offset-2">
    	<div class="col-xl-6">
    		<div class="card">
    			<div class="card-body">
    				<div class="chart-container">
    					<div class="chart has-fixed-height" id="bars_basic"></div>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>	
</div>
<!-- {{dd($data['gph'])}} -->
</body>
</html>

<script type="text/javascript">
var bars_basic_element = document.getElementById('bars_basic');
if (bars_basic_element) {
    var bars_basic = echarts.init(bars_basic_element);
    bars_basic.setOption({
        color: ['#3398DB'],
        tooltip: {
            trigger: 'axis',
            axisPointer: {            
                type: 'shadow'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: [
            {
                type: 'student',
                data: ['total'],
                axisTick: {
                    alignWithLabel: true
                }
            }
        ],
        yAxis: [
            {
                type: 'status'
            }
        ],

        series: [
            {
                name: 'Total ',
                type: 'bar',
                barWidth: '20%',
                data: [
                    {{$data['gph']}}
                  
                ]
            }
        ]
    });
}
</script>