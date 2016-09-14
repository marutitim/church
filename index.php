
<?php
session_start();
if (!isset($_SESSION["user_id"])) {
   header('location:Controller/auth.php');
}

?>
<!DOCTYPE html>
<!--<script src="codebase/app/desktop/init.js" type="text/javascript"></script>-->
<html>
<head>
	<title>CRM System</title>
       <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	 <link rel="stylesheet" type="text/css" href="Controller/include/dhtmlx6/skins/web/dhtmlx.css"/> 
        <link rel="stylesheet" type="text/css" href="Controller/include/dhtmlx6/skins/terrace/dhtmlx.css"/> 
          <link rel="stylesheet" type="text/css" href="Views/css/fontawesome/css/font-awesome.min.css"/> 
        <link rel="stylesheet" type="text/css" href="Views/css/bootstrap/css/bootstrap.min.css"/>
         <script src="Views/jquery/jquery-2.2.1.min.js"></script>
        <script src="Views/css/bootstrap/js/bootstrap.min.js"></script>
        <script src="Controller/include/dhtmlx6/codebase/dhtmlx.js"></script>
</head>
<body>
	
</body>
</html>