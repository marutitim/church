<?php 

     require_once 'config/config.php';
    @$action=$_POST['!nativeeditor_status'];  
    @$id=$_POST['id']; 
    @$event_items=$_POST['text'];
    @$start_date=$_POST['start_date'];
    @$end_date=$_POST['end_date'];
    @$type=$_POST['type'];
 
     switch($action){
        
     case 'inserted'://insert shedule items
        $insert_query = "INSERT INTO events(`event_id`,event_items,start_date,end_date,type)"
             . " VALUES($id,'$event_items','$start_date','$end_date','$type')";
       //  echo $insert_query;exit;
        if (mysqli_query($con, $insert_query)) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error();
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
     break;
     case 'updated': //update shedule items
     
     $query="UPDATE events SET end_date='$end_date',start_date='$start_date',event_items='$event_items',type='$type'"
             . " WHERE id='$id'";
         if (mysqli_query($con, $query)) {
            $msg = "item updated";
        } else {
            $msg ='Error occured';
        }
        echo json_encode(array("response" => $msg));
     break;
     case 'deleted'://delete shedule items
        $query="DELETE FROM events  WHERE id='$id'";
         if (mysqli_query($con, $query)) {
            $msg = "item deleted";
        } else {
            $msg ='Error occured';
        }
        echo json_encode(array("response" => $msg));  
     break;
     case 'filter':
         $filter_value=$_GET['filtervalue'];
            require ('../sheduler/samples/common/connector/scheduler_connector.php');
            require_once("../sheduler/samples/common/connector/db_mysqli.php");
            $mysqli = new mysqli('localhost:3307', 'root', 'kenya1234', 'financials'); 
            $scheduler = new SchedulerConnector($mysqli,"MySQLi");
            $qry = "SELECT * FROM `events` WHERE type=$filter_value";
            echo $qry;exit;
            $scheduler->render_sql($qry, "id","start_date,end_date,event_items,type");
         break;
     default://load shedule shedule items

            require ('../sheduler/samples/common/connector/scheduler_connector.php');
            require_once("../sheduler/samples/common/connector/db_mysqli.php");
            $mysqli = new mysqli('localhost:3307', 'root', 'kenya1234', 'financials'); 
            $scheduler = new SchedulerConnector($mysqli,"MySQLi");
            $qry = "SELECT * FROM `events`";
            $scheduler->render_sql($qry, "id","start_date,end_date,event_items,type");   
     break;
     
     }
     

?>
