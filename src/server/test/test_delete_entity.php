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

$item = new stdClass();
$item->itemid = 1;

$entity = new stdClass();
$entity->key = 'Fallout Boy'';
$entity->items = array($item);

echo "here1<br>\n";
$rslt = deleteEntity($token, $entity);
echo "here2<br>\n";

echo sprintf("result: %d\n", intval($rslt));

?>