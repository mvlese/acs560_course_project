<?php
require_once "lib/nusoap.php";
require_once "methods.php";
 
$server = new soap_server();

$server->configureWSDL('JotWS', 'urn:jot');

$server->register('changePassword',
	array('token' => 'xsd:string', 'newPassword' => 'xsd:string'),
	array('retval' => 'xsd:int'), 'urn:jot');

$server->register('deleteAccount',
	array('token' => 'xsd:string', 'username' => 'xsd:string', 'password' => 'xsd:string'),
	array('retval' => 'xsd:int'), 'urn:jot');

$server->register('deleteEntity',
	array('token' => 'xsd:string', 'entity' => 'xsd:string'),
	array('retval' => 'xsd:int'), 'urn:jot');

$server->register('getByDate',
	array('token' => 'xsd:string', 'date' => 'xsd:string'),
	array('retval' => 'xsd:string'), 'urn:jot');

$server->register('getByType',
	array('token' => 'xsd:string', 'type' => 'xsd:string'),
	array('retval' => 'xsd:string'), 'urn:jot');

$server->register('getEntity',
	array('token' => 'xsd:string', 'entity' => 'xsd:string'),
	array('retval' => 'xsd:string'), 'urn:jot');

$server->register('getSharedEntity',
	array('token' => 'xsd:string', 'fromShareWithUsername' => 'xsd:string'),
	array('retval' => 'xsd:string'), 'urn:jot');

$server->register('logon',
	array('username' => 'xsd:string', 'password' => 'xsd:string'),
	array('retval' => 'xsd:string'), 'urn:jot');

$server->register('register',
	array('username' => 'xsd:string', 'password' => 'xsd:string'),
	array('retval' => 'xsd:int'), 'urn:jot');

$server->register('shareEntity',
	array('token' => 'xsd:string', 'entity' => 'xsd:string', 'toShareWithUsername' => 'xsd:string'),
	array('retval' => 'xsd:int'), 'urn:jot');

$server->register('storeEntity',
	array('token' => 'xsd:string', 'entity' => 'xsd:string'),
	array('retval' => 'xsd:int'), 'urn:jot');


$server->service($HTTP_RAW_POST_DATA);

?>



