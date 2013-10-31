<?php
include_once 'methods2.php';

echo "Initial logon.<br>";
$user = "mlese";
$token = logon($user, "P@ssw0rd");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

echo "<br>Deactivate account.<br>";
$rslt = deleteAccount($token, $user, "P@ssw0rd");
if ($rslt == 0) {
	echo "deactivated $user<br>";
} else {
	echo "$user not deactivated<br>";
}

echo "<br>Register user.<br>";
$token = registerNewUser($user, "P@ssw0rd");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

echo "<br>Logon account.<br>";
$token = logon($user, "P@ssw0rd");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

$user = "jlese";
echo "<br>Register new user: $user.<br>";
$token = registerNewUser($user, "P@ssw0rdYYY");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

echo "<br>Logon account.<br>";
$token = logon($user, "P@ssw0rdYYY");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

$user = "ihlese";
echo "<br>Register new user: $user.<br>";
$token = registerNewUser($user, "P@ssw0rd");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

echo "<br>Logon account.<br>";
$token = logon($user, "P@ssw0rd");
if (strlen($token) > 0) {
	echo "$user logged on: $token<br>";
} else {
	echo "$user not logged on<br>";
}

/*
$token = logon("mlese", "P@ssw0rd");
if (strlen($token) > 0) {
	echo "logged on: $token<br>";
} else {
	echo "not logged on<br>";
}
*/
?>