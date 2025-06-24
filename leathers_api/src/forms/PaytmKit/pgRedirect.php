<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

require_once("./lib/config_paytm.php");
require_once("./lib/encdec_paytm.php");

$checkSum = "";
$paramList = array();

$ORDER_ID = $_POST["ORDER_ID"];
$CUST_ID = $_POST["CUST_ID"];
$INDUSTRY_TYPE_ID = $_POST["INDUSTRY_TYPE_ID"];
$CHANNEL_ID = $_POST["CHANNEL_ID"];
$TXN_AMOUNT = $_POST["TXN_AMOUNT"];

$paramList["MID"] = PAYTM_MERCHANT_MID;
$paramList["ORDER_ID"] = $ORDER_ID;
$paramList["CUST_ID"] = $CUST_ID;
$paramList["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
$paramList["CHANNEL_ID"] = $CHANNEL_ID;
$paramList["TXN_AMOUNT"] = $TXN_AMOUNT;
$paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
$paramList["CALLBACK_URL"] = "https://leathers.tamucommerce.in/api/forms/PaytmKit/pgResponse.php";

$checkSum = getChecksumFromArray($paramList, PAYTM_MERCHANT_KEY);
?>
<html>
<head>
<title>Merchant Check Out Page</title>
<script type="text/javascript">
    function delaySubmit() {
        setTimeout(function () {
            document.f1.submit();
        }, 2000); // Delay submission by 5 seconds
    }
</script>
</head>
<body onload="delaySubmit()">
    <center><h1>Please do not refresh this page...</h1></center>

    <form method="post" action="https://securegw-stage.paytm.in/theia/processTransaction" name="f1">
        <?php
        foreach($paramList as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . $value . '">' . "\n";
        }
        ?>
        <input type="hidden" name="CHECKSUMHASH" value="<?php echo htmlspecialchars($checkSum); ?>">
    </form>

   </body>
</html>
