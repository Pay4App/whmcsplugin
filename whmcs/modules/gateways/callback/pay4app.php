<?php

include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

if (file_exists('../../../dbconnect.php'))
{
    include '../../../dbconnect.php';
} else if (file_exists('../../../init.php'))
    {
        include '../../../init.php';
    } else {
        die('DBConnect.php or init.php not found in modules/gateways/callback/pay4app.php');
    }

$gatewaymodule = "pay4app";

$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); # Checks gateway module is active before accepting callback

$invoiceid  = $_GET["order"];   

if ( is_pay4app_transfer_pending_redirect($GATEWAY["pay4appmerchantid"], $GATEWAY["pay4appsecretkey"]) ){
    //tell confirmation comes soon, redirect to client page
    $systemURL = ($CONFIG['SystemSSLURL'] ? $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL']);
    $systemURL .= "/clientarea.php?action=invoices";
    echo "<center style='margin-top: 40px; font-family: helvetica'><h2>Thank You</h2>Confirmation for your payment is yet to be received, and should be in a moment if you completed in full the payment. You will receive emails to this effect. <p><a href='".$systemURL."'>Click here to continue</a></p></center>";
    exit();
}
else if (is_pay4app_notif($GATEWAY["pay4appmerchantid"], $GATEWAY["pay4appsecretkey"])){
    //if redir, redir to client area
    
    
    
    $amount     = $_GET["amount"];
    $fees       = 0;
    $checkoutid = $_GET['checkout'];

    //Check invoice
    checkCbInvoiceID($invoiceid, $GATEWAY["name"]);
    checkCbTransID($checkoutid);
    
    addInvoicePayment($invoiceid,$checkoutid,$amount,$fee,$gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
    logTransaction($GATEWAY["name"], $_GET, "Successful Pay4App Checkout ".$_GET['checkout']); # Save to Gateway Log: name, data array, status

    if (!isset($_GET['R'])){
        echo json_encode(array("status"=>1));
        exit();
    }
    else{
        $systemURL = ($CONFIG['SystemSSLURL'] ? $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL']);
        header("Location: {$systemURL}/viewinvoice.php?id={$invoiceid}");
        exit();

    }

}



////=========

function is_pay4app_notif($merchant, $apisecret){
    if ( isset($_GET['merchant']) AND isset($_GET['checkout']) AND isset($_GET['order'])
          AND isset($_GET['amount']) AND isset($_GET['email']) AND isset($_GET['phone'])
          AND isset($_GET['timestamp']) AND isset($_GET['digest']) 
        ){ 


        //for readability the concatenation is split over two lines   
        $digest = $_GET['merchant'].$_GET['checkout'].$_GET['order'].$_GET['amount'];
        $digest .= $_GET['email'].$_GET['phone'].$_GET['timestamp'].$apisecret;

        $digesthash = hash("sha256", $digest);

        if ($_GET['digest'] !== $digesthash){

            return FALSE;

          }

      return TRUE;
    }
    else{
      
      return FALSE;

    }
}

function is_pay4app_transfer_pending_redirect($merchant, $apisecret){
 if ( isset($_GET['merchant']) AND isset($_GET['order']) AND isset($_GET['digest']) ){
    $expecteddigest = $_GET['merchant'].$_GET['order'].$apisecret;
    $expecteddigest = hash("sha256", $expecteddigest);
    if ($_GET['digest'] !== $expecteddigest){
      return FALSE;
    }
    return TRUE;
  }
  return FALSE;      
}

?>