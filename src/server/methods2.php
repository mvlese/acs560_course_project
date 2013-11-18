<?php
include_once "logger.php";
include_once "EntityItem.php";
include_once "Entity.php";
include_once "EntityResult.php";
include_once "BusinessLayer.php";
require_once 'htmlpurifier.library/HTMLPurifier.auto.php';

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$businessLayer = new BusinessLayer();

function prepareValue($value, $setToUpper = false) {
	global $purifier;

	$clean_value = $purifier->purify($value);

	$temp = trim($clean_value);
	if ($setToUpper == true) {
		$temp = strtoupper($temp);
	} 
	return $temp;
}

#
#
#
function getError($msg)
{
$errorXml=<<<XML
<error>$msg</error>
XML;

  $xml = new SimpleXMLElement($errorXml);
  return $xml->asXML();

}

#
#
#
function changePassword($token, $newPassword)
{
	global $businessLayer;
	$retval = -1;
	$retval = $businessLayer->changePassword(
				prepareValue($token), prepareValue($newPassword));
	return $retval;
}

#
#
#
function deleteAccount($token, $username, $password)
{
	global $businessLayer;
	$retval = -1;
	$retval = $businessLayer->deactivateUser(
				prepareValue($token),
				prepareValue($username, true),
				prepareValue($password));
	
	return $retval;
}

#
#
#
function logon($username, $password)
{
	global $businessLayer;
	$retval = "";
	logger("entering logon\n");

	$retval = $businessLayer->logon(
				prepareValue($username, true), prepareValue($password));

	logger("logon: token: $retval\n");
  
	return $retval;
}

#
#
#
function registerNewUser($username, $password)
{
	global $businessLayer;
	$retval = "";

	logger("entering registerNewUser\n");

	$retval = $businessLayer->registerNewUser(
				prepareValue($username, true), prepareValue($password));
  
	logger("registerNewUser: token: $retval\n");

  	return $retval;
}

#
#
#
function deleteEntity($token, $entity)
{
	global $businessLayer;
	$retval = -1;

	logger("entering deleteEntity\n");

	$obj = Entity::getEntityFromData($entity);
	$retval = $businessLayer->deleteEntity(prepareValue($token), $obj);
  
	logger("leaving deleteEntity: result: $retval\n");

	return $retval;
}

#
#
#
function getByDate($token, $startDate, $endDate)
{
	global $businessLayer;

	$retval = $businessLayer->getByDate(
				prepareValue($token), prepareValue($startDate), prepareValue($endDate));
	
	return $retval->getData();
}

#
#
#
function getByType($token, $type)
{
	global $businessLayer;

	$retval = $businessLayer->getByType(
				prepareValue($token), prepareValue($type));
	
	return $retval->getData();
}

#
#
#
function getEntity($token, $key)
{
	global $businessLayer;

	logger("entering getEntity\n");

	$retval = $businessLayer->getEntity(
				prepareValue($token), prepareValue($key));

	logger("leaving getEntity\n");

	return $retval->getData();
}

#
#
#
function getSharedEntity($token, $entity, $fromShareWithUsername)
{
	global $businessLayer;
	$retval = new EntityResult();

	$msg = "not implemented";
	$retval->setErrorMessage($msg);
	
	return $retval;
}


#
#
#
function shareEntity($token, $entity, $toShareWithUsername)
{
	global $businessLayer;
	$retval = -1;

	logger("entering shareEntity\n");

	$obj = Entity::getEntityFromData($entity);
	$retval = $businessLayer->shareEntity(
			prepareValue($token), 
			$obj, 
			prepareValue($toShareWithUsername));

	logger("leaving shareEntity\n");

	return $retval;
}

#
#
#
function storeEntity($token, $entity)
{
	global $businessLayer;
	$retval = -1;

	logger("entering storeEntity\n");

	$obj = Entity::getEntityFromData($entity);
	$retval = $businessLayer->storeEntity(prepareValue($token), $obj);

	logger("leaving storeEntity\n");

	return $retval;
}

function getAllKeys($token) 
{
	global $businessLayer;

	logger("entering getAllKeys\n");

	$retval = $businessLayer->getAllKeys(prepareValue($token));

	logger("leaving getAllKeys\n");

	return $retval->getData();	
}
?>
