<?php
 header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");  
     
// Check if the form is submitted 
if ( isset( $_POST['submit'] ) ) { 
 
// retrieve the form data by using the element's name attributes value as key 
 
//echo '<h2>form data retrieved by using the $_REQUEST variable<h2/>';
 
$Order_ID = $_REQUEST['orderid'];
$Customer_ID = $_REQUEST['customerid']; 
$TxnAmount = $_REQUEST['amount'];
$Industry_Type_ID = $_REQUEST['industrytypeid'];
$Channel = $_REQUEST['channel'];
 
// display the results 


  echo '<form  method="post" action="TxnTest.php">' 
.'<input id="orderid" name="orderid" value="'.$Order_ID.'"/>'.
'<input type="customerid" name="customerid" value="'.$Customer_ID.'"/>'.
'<input type="amount" name="amount" value="'.$TxnAmount.'"/>'.
'<input type="industrytypeid" name="industrytypeid" value="'.$Industry_Type_ID.'"/>'.
'<input type="channel" name="channel" value="'.$Channel.'"/>'.

'<input type="submit"/>'.
'</form>';

exit; 
} 
   
?>