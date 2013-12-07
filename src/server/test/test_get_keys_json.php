<?php
include_once '../methods2.php';

echo "Initial logon.<br>";
$user = "mlese";
$token = logon($user, "P@ssw0rd");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

	$rslt = getAllKeys($token);

	$r = new stdClass();
	$r->result = $rslt['result'];
	$r->errorMessage = $rslt['errorMessage'];
	$r->searchKeyItems = array();

	$idx = 0;
    foreach($rslt['searchKeyItems'] as $value) {
    	$item = new stdClass();
    	$item->key = $value['key'];
    	$item->owner = $value['owner'];
    	$item->item_memory_bytes = $value['item_memory_bytes'];
    	$item->modified = $value['modified'];
    	$r->items[$idx++] = $item;
    }
		
	echo json_encode($r);


?>

