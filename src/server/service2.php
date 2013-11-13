<?php
ini_set('output_buffering', 0);

require_once "lib/nusoap.php";
require_once "jot_wsdl.php";

$namespace = 'urn:jot';

#echo "hello";
$server = new soap_server();
$server->configureWSDL('JotWS', $namespace);
$server->wsdl->schemaTargetNamespace = $namespace;

JotWsdl::init($server);

# Start the service.		
$server->service($HTTP_RAW_POST_DATA);

?>



