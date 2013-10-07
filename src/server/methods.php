<?php

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
  $retval = -1;

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
  $retval = "";

  $msg = "not implemented";
  $retval = getError($msg);

  return $retval;
}

#
#
#
function getByType($token, $type)
{
  $retval = "";

  $msg = "not implemented";
  $retval = getError($msg);

  return $retval;
}

#
#
#
function getEntity($token, $entity)
{
  $retval = "";

  $msg = "not implemented";
  $retval = getError($msg);

  return $retval;
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
function logon($username, $password)
{
  $retval = "atoken";

  return $retval;
}

#
#
#
function register($username, $password)
{
  $retval = -1;

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
