<?php
   
    header("Access-Control-Allow-Origin: *");
   
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
  $sql = "SELECT
            p.fvoriginal,
            p.prdMrp,
            p.prdPrice,
            p.prdName,
            p.prdId,
            p.prdsubcatId,
            c.catId,
            c.catName,
            sc.subcatName
        FROM
            tblproducts p
        LEFT JOIN
            tblsubcategories sc ON p.prdsubcatId = sc.subcatId
        LEFT JOIN
            tblcategories c ON sc.subcatCat= c.catId
	WHERE
   	    p.status = 'TRUE'";
    $result = mysqli_query($con,$sql) or die("Error in select products." . mysqli_error($connection));
 
    //create an array
    $productsarray = array();
 
     while($row =mysqli_fetch_assoc($result))
    {
        $productsarray[] = $row;
    }
 
    echo json_encode($productsarray);
 
?>