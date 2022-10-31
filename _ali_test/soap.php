<?php


$url	= $_POST['url'];
$user	= $_POST['user'];
$pass	= $_POST['pass'];

if(isset($url)){
include 'NTLMSoapClient.php';
try {





//http://moas.aljouf.com.sa:8101/OpenCardR/xppservice.svc?wsdl
//aljouf\axservice
//Jadco@$ri@ad
$client = new NTLMSoapClient($url,$user,$pass);



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
}
echo '<hr>';
echo time();
?>