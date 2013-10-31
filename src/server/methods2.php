<?php
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
	$retval = -1;
	
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
	
	$retval = $businessLayer->logon(prepareValue($username, true), prepareValue($password));
  
	return $retval;
}

#
#
#
function registerNewUser($username, $password)
{
	global $businessLayer;
	$retval = "";

	$retval = $businessLayer->registerNewUser(prepareValue($username, true), prepareValue($password));
  
  	return $retval;
}

#
#
#
function deleteEntity($token, $entity)
{
  $retval = -1;

  return $retval;
}

#
#
#
function getByDate($token, $date)
{
	$retval = new EntityResult();
	
	$retval->setResult(-1);
	$retval->setErrorMessage('not implemented');
	
	return $retval->getData();
	
	return $retval;
}

#
#
#
function getByType($token, $type)
{
	$retval = new EntityResult();
	
	$retval->setResult(-1);
	$retval->setErrorMessage('not implemented');
	
	return $retval->getData();
	
	return $retval;
}

#
#
#
function getEntity($token, $entity)
{
	$retval = new EntityResult();
	
	$retval->setResult(0);
	$retval->setErrorMessage('');
	$entity1 = new Entity();
	$entity1->setKey('key1');
	$retval->addEntity($entity1);
/*
	$entity2 = new Entity();
	$entity2->setKey('key2');
	$retval->addEntity($entity2);

	$entity3 = new Entity();
	$entity3->setKey('key3');
	$retval->addEntity($entity3);

	$entity4 = new Entity();
	$entity4->setKey('key4');
	$retval->addEntity($entity4);
*/	
	return $retval->getData();
}

#
#
#
function getSharedEntity($token, $entity, $fromShareWithUsername)
{
  $retval = "";

  $msg = "not implemented";
  $retval = getError($msg);

  return $retval;
}


#
#
#
function shareEntity($token, $entity, $toShareWithUsername)
{
  $retval = -1;

  return $retval;
}

#
#
#
function storeEntity($token, $entity)
{
  $retval = -1;

  return $retval;
}


?>
