<?php

require_once 'config/config.php';

switch ($_GET['action']) {
    case '1':
        $username = $_GET['username'];
        $password = $_GET['password'];

        if ($username && $password) {
            $qry = "SELECT m.name,l.`password` ,l.login_id,l.previlages,m.image
FROM login l
RIGHT JOIN members  m on m.reg_no=l.reg_no
 WHERE l.username='{$username}'";
            // 
            $result = mysqli_query($con, $qry)or die(mysqli_error($con));
            $rowcheck = mysqli_num_rows($result);
            if ($rowcheck != 0) {
                if ($row = mysqli_fetch_assoc($result)) {
                    if ($password === $row['password']) {
                        $_SESSION['user_id'] = $row['login_id'];
                       
                        $_SESSION['prevelages'] = $row['previlages'];
                        $arr = explode(' ',trim($row['name']));
                        $_SESSION['username'] = $arr[0];
//                        $_SESSION['role'] = $row['role'];
                        $_SESSION['image'] = "http://" . $_SERVER['HTTP_HOST'] . "/financials/Controller/uploads/2016/{$row['image']}";
                        
                       // echo  $_SESSION['image'];exit;
                        $msg = "<div class='alert alert-success'>Welcome back " . $_SESSION['username'] . "</div>";
                    } else {
                        $msg = "<div class='alert alert-warning'>Your UserId or Password is Wrong</div>";
                    }
                }
            } else {
                $msg = "<div class='alert alert-danger'>Access denied.Please contact the system administrator </div>";
            }
        } else {
            $msg = "<div class='alert alert-info'>Please Enter UserId and Password</div>";
        }
      
        if ($msg) {
            $data['data'] = array('msg' => $msg, 'success' => true);
        } else {
            $data['data'] = array('msg' => $msg, 'success' => false);
        }
        echo json_encode($data);
        break;

    default:
        break;
}
       