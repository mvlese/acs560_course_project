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

$rslt = getByDate($token, '2013-11-06', '2013-11-06');
var_dump ($rslt);

echo "result: " . $rslt['result'] . "<br>\n";
$entities = $rslt['searchKeyItems'];
echo sprintf("count: %d<br>\n", count($entities));
foreach($entities as $key => $value) {
	echo "key: " . $value['key'] . "<br>";
}
?>

