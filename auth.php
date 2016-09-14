<?php
session_start();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
          <link rel="shortcut icon" href="../Views/imgs/error.png"  type="image/x-icon" >
         <meta name="viewport" content="width=device-width">
         <link rel="stylesheet" type="text/css" href="../Views/css/fontawesome/css/font-awesome.min.css"/> 
        <link rel="stylesheet" type="text/css" href="../Views/css/bootstrap/css/bootstrap.min.css"/>
        <script src="../Views/jquery/jquery-2.2.1.min.js"></script>
        <script src="../Views/css/bootstrap/js/bootstrap.min.js"></script>
        <title>Login</title>
        <script></script>
    </head>                  
    <body >
<div class="container">    
        <div id="loginbox"  class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" style="margin-top: 40%;" >
                <div class="panel-heading" style="background-color:#00cc99">
                        <div class="panel-title" style="color:#ffffff;">Sign In</div>
                        
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                            
                        <form id="loginform" method="post" class="form-horizontal" >
                                    
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input id="username" type="text" class="form-control" name="username" value="" placeholder="UserId">                                        
                                    </div>
                                
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                        <input id="user_password" type="password" class="form-control" name="password" placeholder="password">
                                    </div>
                                 <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="col-sm-12 controls">
                                  <button type="submit" id="btn-fblogin" name="login" class="btn btn-primary" style="background-color:#00cc99;"><label>Login</label></button>
                                       <div id="progressbar"></div>
                                       <div class="input-prepend" id="input-prepend">
                                           </div>
                                    </div>
                                </div>   
                            </form>     



                        </div>                     
                    </div>  
        </div>
            </div>

                        <script>

                jQuery(document).ready(function () {
                    $("#loginform").submit(function (event) {
                         event.preventDefault();
                        var username = $('#username').val();
                        var password = $('#user_password').val();
                        $.get("login.php?action=1&username=" + username + "&password="+password,
                            function (data) {
                               
                                if(data.data.success=true)
                                {
                                   $('#input-prepend').html(data.data.msg); 
                                       window.setTimeout(function(){
                                        // Move to a new location or you can do something else
                                      window.location.href="http://"+location.host+"/financials/Controller/pages/dashboard.php"
                                   $('#progressbar').progressbar();
                                    }, 10);
                                    
                                }
                                else
                                {
                                 $('#input-prepend').html(data.data.msg);    
                                }
                           
                   //  $('#progressbar').progressbar();  
                    },'json');
                });

                  });
            </script>
   </body>
</html>