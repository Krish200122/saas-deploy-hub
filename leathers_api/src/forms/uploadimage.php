<?php
  if(!empty($_FILES['file_attachment']['name']))
  {
    $target_dir = "../assets/upload/";
    if (!file_exists($target_dir))
    {
      mkdir($target_dir, 0777);
    }
    $target_file =
      $target_dir . basename($_FILES["file_attachment"]["name"]);
      $Price = ($_FILES["file_attachment"]["name"]);
      $Id = ($_FILES["file_attachment"]["name"]);
      $Name = basename($_FILES["file_attachment"]["name"]);
    $imageFileType = 
      strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if file already exists
    
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
    if ($_FILES["file_attachment"]["size"] > 500000000) {
      echo json_encode(
         array(
           "status" => 0,
           "data" => array(),
           "msg" => "Sorry, your file is too large."
         )
       );
      die();
    }
    if (
      move_uploaded_file(
        $_FILES["file_attachment"]["tmp_name"], $target_file
      )
    ) {
        
        
      echo json_encode(
        array(
          "status" => 1,
          "data" => array(),
          "msg" => "The file " . 
                   basename( $_FILES["file_attachment"]["name"]) .
                   " has been uploaded."));
                   
                   require_once 'config.php';
        
         $query= "INSERT INTO  tblproducts(prdId,prdName,prdImage,prdPrice,prdCategory,prdMrp,prdDiscount,prdcgst,prdsgst,prdDescription) VALUES('$Id','$Name','$target_file','$Price','','','','','','')";
   if(!mysqli_query($con,$query))
   echo mysqli_error;
   
    } else {
      echo json_encode(
        array(
          "status" => 0,
          "data" => array(),
          "msg" => "Sorry, there was an error uploading your file."
        )
      );
    }
  }
?>