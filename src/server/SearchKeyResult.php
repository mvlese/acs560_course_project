<?php
require_once 'AbstractResult.php';

class SearchKeyResult extends AbstractResult 
{
	
	public function __construct() {
		logger("in SearchKeyResult");
		parent::__construct();
	}
	
	public function getData() {
		return parent::getWebData('searchKeyItems');
	}

}
?>