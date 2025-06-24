<?php
    
    header("Access-Control-Allow-Origin: *");
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql ="SELECT catId, catName, '' subCategories FROM tblcategories";
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));
    
    //create an array
    $category = array();
    while($row =mysqli_fetch_assoc($result))
    {
        
        $row["subCategories"] =  array();

        $sqlSubCategories = "SELECT subcatId, subcatName from tblsubcategories where subcatCat=".$row["catId"]; 
        
        $resultSubCat = mysqli_query($con, $sqlSubCategories) or die("Error in Selecting " . mysqli_error($connection));

        $subCategories = array();
        while($rowSubCat =mysqli_fetch_assoc($resultSubCat))
        {
            $row["subCategories"][] = $rowSubCat;
        }

        $category[] = $row;
        

    }
    
    echo json_encode($category);

?>