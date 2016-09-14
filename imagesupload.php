<?php
  session_start();
if (@$_REQUEST["action"] == "loadImage") {
    header("Content-Type: image/jpg");
   //$reg_no = $_SESSION['reg_no'];
    $image=$_GET['itemValue'];
    print_r(file_get_contents("uploads/2016/".$image));
} else {

    @$filename = $_FILES['file']['name'];
    @$filetype = $_FILES['file']['type'];
    @$file = $_FILES['file']['tmp_name'];
    @$filename = strtolower($filename);
    @$filetype = strtolower($filetype);
    //check if contain php and kill it 
    $pos = strpos($filename, 'php');
    if (!($pos === false)) {
        $msg = 'wrong file type';
    }
    //get the file ext
    $file_ext = strrchr($filename, '.');

    //check if its allowed or not
    $whitelist = array(".jpg", ".jpeg", ".gif", ".png");
    if (!(in_array($file_ext, $whitelist))) {
        $msg = 'not allowed extension,please upload images only';
    }
    if (!@$msg) {
        $uploaddir = 'uploads/' . date("Y") . '/';
        if (file_exists($uploaddir)) {
            
        } else {
            mkdir($uploaddir, 0777);
        }
        $reg_no = $_SESSION['reg_no'];
        $img = explode('.', $filename);
        $savedimage = $reg_no . '.' . $img[1];
        $movedimage = move_uploaded_file($file, $uploaddir . $savedimage);
        header("Content-Type: text/html; charset=utf-8");
        print_r("{state: true, itemId: '".@$_REQUEST["itemId"]."', itemValue: '".$savedimage."'}");
    
        $con =new mysqli("localhost:3307","root","kenya1234","financials");
        if (!$con)
          {
          die('Could not connect: ' . mysqli_error());
          }
        $qry = "UPDATE members SET image= '{$savedimage}' WHERE reg_no = '{$reg_no}'";
        $res = mysqli_query($con,$qry) or die(mysqli_error() . $qry);
       // $msg = 'Photo Uploaded successfully!';
    }
 //   $data['data'] = array('success' => $msg);

  //  echo json_encode($data);
}


