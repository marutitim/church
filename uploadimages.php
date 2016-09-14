<?php

error_reporting(E_ERROR | E_PARSE);
$image_id = $_REQUEST['id'];
if (!empty($_FILES)) {
    $upload_image_to_folder = "assets_pic/";

    $file = $_FILES['file'];
    $file_name = $file['name'];
    $error = '';


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
        $uploaddir = $upload_image_to_folder.'/' . date("Y") . '/';
        if (file_exists($uploaddir)) {
            
        } else {
            mkdir($uploaddir, 0777);
        }
       
        $img = explode('.', $filename);
        $savedimage = $image_id . '.' . $img[1];
        $movedimage = move_uploaded_file($file, $uploaddir . $savedimage);
       // header("Content-Type: text/html; charset=utf-8");
      //  print_r("{state: true, itemId: '".@$_REQUEST["itemId"]."', itemValue: '".$savedimage."'}");
    
        $con =new mysqli("192.168.1.200","root","kenya1234","financials");
        if (!$con)
          {
          die('Could not connect: ' . mysqli_error());
          }
        $qry = "UPDATE assests SET image= '{$savedimage}' WHERE id = '{$image_id}'";
        $res = mysqli_query($con,$qry) or die(mysqli_error($con) . $qry);
          print_r("{state: true, name:'success'}");
} 
 else {
    //$error = 'No file selected';
    print_r("{state: false, name:'No file selected'}");
}
}

