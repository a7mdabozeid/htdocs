<?php


    $email = 'johann.******@gmail.com';
    $encrypt = '******************************';
    $notification_id = '**************';
    $random = '********************';
    $senddate = '2013-09-09T00:00:00';
    $synchrotype = 'NOTHING';
    $uidkey = 'EMAIL';


    $params = array(
        'arg0' => array(
            'content' => array( 1 => 'mon_test'),
            'dyn' => array( 'FIRSTNAME' => 'yoyo'),
            'email' => $email,
            'encrypt' => $encrypt,
            'notificationId' => $notification_id,
            'random' => $random,
            'senddate' => $senddate,
            'synchrotype' => $synchrotype,
            'uidkey' => $uidkey
        )
    );


    $client = new       SoapClient('https://fa-emga-test-saasfaprod1.fa.ocs.oraclecloud.com/xmlpserver/services/ExternalReportWSSService?WSDL', array(  'trace' => 1, 'exceptions' => 0  ) );

    $res = $client->sendObject( $params );

echo "<br /><br /><br />";
echo "REQUEST 1 :" . htmlspecialchars($client->__getLastRequest()) . "<br />";
echo "RESPONSE 1 :" . htmlspecialchars($client->__getLastResponse()) . "<br /><br /><br />";