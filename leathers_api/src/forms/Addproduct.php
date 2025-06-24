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
    $catid = $_POST["catid"];
    $Price = $_POST["price"];
    $Description = $_POST["description"];
    $Productname = $_POST["productname"];

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
    
        $query ="SELECT ifNull(Max(prdId),0) + 1 As MaxPrdId from tblproducts";

        $res =mysqli_query($con,$query);
        $data =mysqli_fetch_array($res);
        $Prd_Id = $data['MaxPrdId'];     
        
        $query= "INSERT INTO tblproducts(prdId,prdName,prdCategory,prdImage,prdPrice,prdDescription) VALUES($Prd_Id,'$Productname',$catid,'https://swatnkaquatics.com/assets/images/$Name',$Price,'$Description')";
        
        if(mysqli_query($con,$query))
        {
            echo json_encode(
                array(
                  "status" => 1,
                  "data" => array(),
                  "msg" => "Product uploaded successfully."
                )
            );      
        }
        else{

            echo json_encode(
                array(
                  "status" => 0,
                  "data" => array(),
                  "msg" => "Sorry there was an error during insert product.".$Prd_Id
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