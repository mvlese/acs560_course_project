<?php
#include_once 'methods2.php';
$client = new SoapClient("http://jot.leseonline.net/service2.php?wsdl");

echo "Initial logon.<br>";
$user = "mlese";
$token = $client->logon($user, "P@ssw0rd");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

$key = array();
$key[0] = "Hoss Cartwright";
$key[1] = "wolf_00";

for ($i = 0; $i < count($key); $i++) {
    $client = new SoapClient("http://jot.leseonline.net/service2.php?wsdl");
    $rslt = $client->getEntity($token, $key[$i]);
    echo sprintf("\n<br>getEntity result: %d<br>\n", intval($rslt->result));
    foreach ($rslt->entities as $value) {
    	echo $value->key . "<br>";
    	foreach($value->items as $value1) {
    		echo $value1->itemid . "<br>";
    		echo $value1->itemtype . "<br>";
    		echo $value1->annotation . "<br>";
    		$str = $value1->bdata;
    		if (strlen($str) > 0) {
        		echo strlen($str) . "<br>";
        		$bin = base64_decode($str);
        		echo strlen($bin) . "<br>";
    		}
    	}
    }
}

?>