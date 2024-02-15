
<!DOCTYPE html>
    <head>
        <title>{{env('APPLICATION_TITLE')}}</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="https://www.ramomcoder.com/assets/vendor/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://www.ramomcoder.com/assets/vendor/font-awesome/css/all.min.css">
        <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.7.6/css/react-select.css"/>
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    </head>
    <body oncontextmenu="return false;">
        <style type="text/css">
            body {
                padding-top: 50px;
            }

            .navbar-inverse {
                background-color: #313131;
                border-color: #404142;
            }
            .navbar-header h4 {
                margin: 0;
                padding: 15px 15px;
                color: #c4c2c2;
            }
            .navbar-right h5 {
                margin: 0;
                padding: 9px 5px;
                color: #c4c2c2;
            }
            .navbar-inverse .navbar-collapse, .navbar-inverse .navbar-form{
                border-color: transparent;
            }
        </style>
                <nav id="nav-tool" class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <h4><i class="fab fa-chromecast"></i> Live Class Title : {{$row->topic}}</h4>
                </div>
                <div class="navbar-form navbar-right">
                    <h5><i class="far fa-user-circle" style=""></i> Host By : {{$row->staff_name}}</h5>
                </div>
            </div>
        </nav>
        <script src="https://source.zoom.us/1.7.8/lib/vendor/react.min.js"></script>
        <script src="https://source.zoom.us/1.7.8/lib/vendor/react-dom.min.js"></script>
        <script src="https://source.zoom.us/1.7.8/lib/vendor/redux.min.js"></script>
        <script src="https://source.zoom.us/1.7.8/lib/vendor/redux-thunk.min.js"></script>
        <script src="https://source.zoom.us/1.7.8/lib/vendor/jquery.min.js"></script>
        <script src="https://source.zoom.us/1.7.8/lib/vendor/lodash.min.js"></script>
        <script src="https://source.zoom.us/zoom-meeting-1.7.8.min.js"></script>
        <script type="text/javascript">

            document.onkeydown = function(e) {
              if(event.keyCode == 123) {
                 return false;
              }
              if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                 return false;
              }
              if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
                 return false;
              }
              if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                 return false;
              }
              if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                 return false;
              }
            }
            
            ZoomMtg.preLoadWasm();
            ZoomMtg.prepareJssdk();
            var meetConfig = {
                apiKey: "{{$row->zoom_api_key}}",
                apiSecret: "{{$row->zoom_secret_key}}",
                meetingNumber: "{{$row->meeting_id}}",
                userName: "{{$row->staff_name}}",
                passWord: "{{$row->meeting_password}}",
                leaveUrl: "{{route('user-staff.live_class')}}",
                role: parseInt(1, 10)
            };
            var signature = ZoomMtg.generateSignature({
                meetingNumber: meetConfig.meetingNumber,
                apiKey: meetConfig.apiKey,
                apiSecret: meetConfig.apiSecret,
                role: meetConfig.role,
                success: function(res){
                    console.log(res.result);
                }
            });
            ZoomMtg.init({
                leaveUrl: meetConfig.leaveUrl,
                isSupportAV: true,
                success: function () {
                    ZoomMtg.join(
                        {
                            meetingNumber: meetConfig.meetingNumber,
                            userName: meetConfig.userName,
                            signature: signature,
                            apiKey: meetConfig.apiKey,
                            passWord: meetConfig.passWord,
                            success: function(res){
                                $('#nav-tool').hide();
                            },
                            error: function(res) {
                                console.log(res);
                            }
                        }
                    );
                },
                error: function(res) {
                    console.log(res);
                }
            });
        </script>
    </body>
</html>
