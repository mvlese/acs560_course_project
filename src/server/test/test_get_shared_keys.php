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

$rslt = getSharedKeys($token);
//var_dump ($rslt);

echo "result: " . $rslt['result'] . "<br>\n";
$items = $rslt['searchKeyItems'];
echo sprintf("count: %d<br>\n", count($items));
foreach($items as $value) {
	echo sprintf("%s / %s / %s / %s<br>",
		$value['key'],
		$value['owner'],
		$value['item_memory_bytes'],
		$value['modified']
		);
}
?>

