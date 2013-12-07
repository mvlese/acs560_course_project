<?php
$client = new SoapClient("http://jot.leseonline.net/service2.php?wsdl");

echo "Initial logon.<br>";
$user = "mlese";
$token = $client->logon($user, "P@ssw0rd");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

$client = new SoapClient("http://jot.leseonline.net/service2.php?wsdl");
    
$entity = new stdClass();
$entity->key = "Hoss Cartwright";
$entity->item_memory_bytes = 0;  # don't care
$entity->modified = 'dontcare';

$item = new stdClass();
$item->itemid = 1;
$item->itemtype = 'image';
$item->annotation = '';
$item->bdata = '';
    
$entity->items = array($item);
$toShareWithUsername = "DWOLF";
    
$rslt = $client->shareEntity($token, $entity, $toShareWithUsername);

echo "result is: " . $rslt . "<br>";

?>