<?php
include 'NTLMSoapClient.php';
try {




$wsdlUrl = 'https://fa-emga-test-saasfaprod1.fa.ocs.oraclecloud.com/xmlpserver/services/ExternalReportWSSService?WSDL';
$client = new NTLMSoapClient($wsdlUrl,'WMS','12345678');


echo "<pre>";
print_r($client->__getFunctions());
echo "</pre>";

echo "<pre>";
print_r($client->__getTypes());
echo "</pre>";

	
}
catch(Exception $e) {
    echo $e->getMessage();
}

?>