<?php

ini_set('display_errors', '1');

function addImageRecord($id, $foto,$con) {
     $ImageQuery = "UPDATE  assests  SET image='$foto' WHERE id=$id";
     $advImageinsert = mysqli_query($con,$ImageQuery) or die("sql error when inserting new record  " . mysqli_error($con).$ImageQuery);

    return $advImageinsert;
}

function getNoImagesforProduct($id,$con) {
          $legth = strlen($id);

        if ($legth == 15) {
            $ID = substr($id, 0, 1);
        } else {
            $ID = $id;
        }
   
        $query="SELECT image FROM assets WHERE id='{$ID}'";        
        $imagesResult=mysqli_query($con,$query);
      
       return mysqli_num_rows($imagesResult);
}
