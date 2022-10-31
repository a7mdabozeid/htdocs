<?php


$client = new SoapClient('http://api.notificationmessaging.com/NMSOAP/NotificationService?wsdl', array( 'trace' => 1, 'exceptions' => 0 ) );

$email = 'john.smith@example.com';
$encrypt = 'AAAAAAAAAAAAAAAAAAAAAAAAAAA';
$notification_id = 123456789;
$random = 'BBBBBBBBBBBB';
$senddate = '2013-09-09T00:00:00';
$synchrotype = 'NOTHING';
$uidkey = 'EMAIL';

$content = array();
$content[] = array(
    2 => 'TEST'
);

$dyn = array();
$dyn[] = array(
    'FIRSTNAME' => 'John'
);
$dyn[] = array(
    'LASTNAME' => 'Smith'
);

$params = array(
    'email' => $email,
    'encrypt' => $encrypt,
    'notificationId' => $notification_id,
    'random' => $random,
    'senddate' => $senddate,
    'synchrotype' => $synchrotype,
    'uidkey' => $uidkey,
    'content' => $content,
    'dyn' => $dyn
);

$res = $client->__soapCall( 'sendObject', array( $email, $encrypt, $notification_id, $random, $senddate, $synchrotype, $uidkey, $content, $dyn ) );
var_dump($res);
?>