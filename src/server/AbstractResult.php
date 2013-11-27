<?php
abstract class AbstractResult {
	private $result;
	private $errorMessage;
	private $items;
	
	public function __construct() {
		$this->result = 0;
		$this->errorMessage = '';
		$this->items = array();
	}

	public function addItem($item) {
		$idx = count($this->items);
		$this->items[$idx] = $item;
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

	protected function getWebData($arrayName) {
		$retval = array();

		$retval['result'] = $this->result;
		$retval['errorMessage'] = $this->errorMessage;
		
		$itemList = array();
        $idx = 0;
        foreach($this->items as $value) {
                $itemList[$idx] = $value->getData();
                $idx++;
        }
        $retval[$arrayName] = $itemList;
		return $retval;
	}
	
	abstract public function getData();

}

?>