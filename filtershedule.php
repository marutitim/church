<?php 

     require_once 'config/config.php';
    @$action=$_POST['action'];  
    @$filtervalue=$_POST['filtervalue']; 
   
     switch($action){
        
      default:
         $filter_value=$_GET['filtervalue'];
            require ('../sheduler/samples/common/connector/scheduler_connector.php');
            require_once("../sheduler/samples/common/connector/db_mysqli.php");
            $mysqli = new mysqli('localhost:3307', 'root', 'kenya1234', 'financials'); 
            $scheduler = new SchedulerConnector($mysqli,"MySQLi");
            $qry = "SELECT * FROM `events` WHERE type=$filter_value";
         
            $scheduler->render_sql($qry, "id","start_date,end_date,event_items,type");
         break;
     
     }
     

?>
