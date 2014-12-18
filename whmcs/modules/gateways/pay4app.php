<?php
function pay4app_config()
{
    $configarray = array
    (
        "FriendlyName" => array("Type" => "System", "Value" => "Pay4App"),
        "pay4appmerchantid" => array("FriendlyName" => "Merchant ID", "Type" => "text", "Size" => "50"),
        "pay4appsecretkey" => array("FriendlyName" => "Group", "Type" => "text", "Size" => "100")
    );
    return $configarray;
}

function pay4app_link($params)
{

    
    $merchantid = $params['pay4appmerchantid'];
    $orderid        = $params['invoiceid'];
    $amount         = $params['amount'];
    $signature  = hash("sha256", $merchantid.$orderid.$amount.$params['pay4appsecretkey']);
    $redirect       = $params['systemurl'] . '/modules/gateways/callback/pay4app.php?R';
    $transferpending = $redirect;

    $pay4app_params = array(

        "merchantid"        => $merchantid,
        "orderid"               => $orderid,
        "amount"                => $amount,
        "signature"         => $signature,
        "redirect"              => $redirect,
        "transferpending"   => $transferpending
        );
        
    $code = '
    <form action="https://pay4app.com/checkout.php" method="post">';
        foreach ($pay4app_params as $key => $value)
        {
            $code .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />' . "\n";
        }
    $code .= '<input type="submit" value="Pay online with EcoCash, VISA or ZimSwitch">
    </form>';

    

    return $code;
}

?>