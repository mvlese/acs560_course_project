<?php
include_once 'AbstractResult.php';

class EntityResult extends AbstractResult {
/*	
	private $resut;
	private $errorMessage;
	private $entities;

	public function __construct() {
		$this->result = 0;
		$this->errorMessage = '';
		$this->entities = array();
	}

	public function addEntity($entity) {
		$idx = count($this->entities);
		$this->entities[$idx] = $entity;
	}

	public function getResult() {
		return $this->result;
	}

	public function setResult($value) {
		$this->result = $value;
	}

	public function getErrorMessage() {
		return $this->errorMessage;
	}

	public function setErrorMessage($value) {
		$this->errorMessage = $value;
	}
*/
	public function getData() {
		return parent::getWebData('entities');
	}

}

?>
