<?php

  if(!empty($_FILES['file_attachment']['name'])) 
  {

    $target_dir = "../assets/images/";

    if (!file_exists($target_dir))
    {
      mkdir($target_dir, 0777);
    }

    $target_file = $target_dir . basename($_FILES["file_attachment"]["name"]);
    $Name = basename($_FILES["file_attachment"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $CatName = $_POST["categoryname"];
    $CatID = $_POST["categoryid"];

    if (file_exists($target_file)) {
      echo json_encode(
         array(
           "status" => 0,
           "data" => array()
           ,"msg" => "Sorry, file already exists."
         )
      );
      die();
    }

    // Check file size
    if ($_FILES["file_attachment"]["size"] > 50000000) {
      echo json_encode(
         array(
           "status" => 0,
           "data" => array(),
           "msg" => "Sorry, your file is too large."
         )
       );
      die();
    }

    if (move_uploaded_file($_FILES["file_attachment"]["tmp_name"], $target_file)) 
    {
        
      require_once 'config.php';

        
        $query= "UPDATE tblcategories SET catId = $CatID , catName = '$CatName', catImage = 'https://swatnkaquatics.com/assets/images/$Name' WHERE catId = $CatID";
        
        if(mysqli_query($con,$query))
        {
            echo json_encode(
                array(
                  "status" => 1,
                  "data" => array(),
                  "msg" => "Product updated successfully."
                )
            );      
        }
        else{

            echo json_encode(
                array(
                  "status" => 0,
                  "data" => array(),
                  "msg" => "Sorry there was an error during update product.$Name"
                )
            );      

        }

    }
    else{

        echo json_encode(
            array(
              "status" => 0,
              "data" => array(),
              "msg" => "Sorry there was an error during upload image."
            )
        );      

    }

  }

?>