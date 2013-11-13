<?php

class EntityResult {
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

	public function getData() {
		$retval = array();

		$retval['result'] = $this->result;
		$retval['errorMessage'] = $this->errorMessage;
		
		$entityList = array();
        $idx = 0;
        foreach($this->entities as $key => $value) {
                $entityList[$idx] = $value->getData();
                $idx++;
        }
        $retval['entities'] = $entityList;
		return $retval;
	}

}

?>
