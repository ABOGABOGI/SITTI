<?php
if (!@include('XML/RPC.php')) {
    die('Error: cannot load the PEAR XML_RPC class');
}
print "XML RPC OK<br/>";
   
$xmlRpcHost = 'localhost';
$webXmlRpcDir = '/openx/www/api/v2/xmlrpc/';
$serviceUrl = $webXmlRpcDir;
$username = 'admin';
$password = 'admin';

$debug = true;

function returnXmlRpcResponseData($oResponse)
{
    if (!$oResponse->faultCode()) {
        $oVal = $oResponse->value();
        $data = XML_RPC_decode($oVal);
        return $data;
    } else {
        die('Fault Code: ' . $oResponse->faultCode() . "\n" .
         'Fault Reason: ' . $oResponse->faultString() . "\n");
    }
}

$oClient = new XML_RPC_Client($serviceUrl, $xmlRpcHost);
$oClient->setdebug($debug);


// Logon
$aParams = array(
    new XML_RPC_Value($username, 'string'),
    new XML_RPC_Value($password, 'string')
);
$oMessage  = new XML_RPC_Message('ox.logon', $aParams);
$oResponse = $oClient->send($oMessage);
if (!$oResponse) {
    die('Communication error: ' . $oClient->errstr);
}
$sessionId = returnXmlRpcResponseData($oResponse);
echo '*** User logged on with session Id : ' . $sessionId . "<br>\n";


// Get an advertiser
$aParams = array(
    new XML_RPC_Value($sessionId, 'string'),
    new XML_RPC_Value(2, 'int')
);
$oMessage = New XML_RPC_Message('ox.getPublisher', $aParams);
$oResponse = $oClient->send($oMessage);
print_r(returnXmlRpcResponseData($oResponse));

echo "<br/>\n";

// Logoff
$aParams = array(new XML_RPC_Value($sessionId, 'string'));
$oMessage = New XML_RPC_Message('ox.logoff', $aParams);
$oResponse = $oClient->send($oMessage);
echo "*** User with session Id : $sessionId logged off<br>\n";

?>