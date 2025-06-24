<?php
 header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");  
     
   $Order_ID = $_REQUEST['orderid'];
$Customer_ID = $_REQUEST['customerid']; 
$TxnAmount = $_REQUEST['amount'];
$Industry_Type_ID = $_REQUEST['industrytypeid'];
$Channel = $_REQUEST['channel'];  
   

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Merchant Check Out Page</title>
<meta name="GENERATOR" content="Evrsoft First Page">
</head>
<body>
	<h1>Merchant Check Out Page</h1>
	<pre>
	</pre>
	<form method="post" action="pgRedirect.php">
		<table border="1">
			<tbody>
				<tr>
					<th>S.No</th>
					<th>Label</th>
					<th>Value</th>
				</tr>
				<tr>
					<td>1</td>
					<td><label>ORDER_ID:</label></td>
					<td><input id="ORDER_ID" tabindex="1" maxlength="20" size="20"
						name="ORDER_ID" autocomplete="off" 
						value="<?php echo $Order_ID?>">
					</td>
				</tr>
				<tr>
					<td>2</td>
					<td><label>CUSTID :</label></td>
					<td><input id="Customer_Id" tabindex="2" maxlength="12" size="12" name="Customer_Id" autocomplete="off"  value="<?php echo $Customer_ID?>"></td>
				</tr>
				<tr>
					<td>3</td>
					<td><label>INDUSTRY_TYPE_ID :</label></td>
					<td><input id="INDUSTRY_TYPE_ID" tabindex="4" maxlength="12" size="12" name="INDUSTRY_TYPE_ID" autocomplete="off"  value="<?php echo $Industry_Type_ID?>"></td>
				</tr>
				<tr>
					<td>4</td>
					<td><label>Channel :</label></td>
					<td><input id="CHANNEL_ID" tabindex="4" maxlength="12"
						size="12" name="CHANNEL_ID" autocomplete="off"  value="<?php echo $Channel?>">
					</td>
				</tr>
				<tr>
					<td>5</td>
					<td><label>txnAmount:</label></td>
					<td><input id="TXN_AMOUNT" tabindex="10"
						type="text" name="TXN_AMOUNT" 
						value="<?php echo $TxnAmount?>">
					</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><input value="CheckOut" type="submit"	onclick=""></td>
				</tr>
			</tbody>
		</table>
	</form>
</body>
</html>