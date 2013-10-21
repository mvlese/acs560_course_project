<?php

class EntityItem {

    private $itemid;
    private $itemtype;
    private $annotation;
    private $bdata;

    public function EntityItem() {
        $this->itemid = -1;
        $this->itemtype = 'text';
        $this->annotation = '';
        $this->bdata = '';
    }

    public function getData() {
        $retval = array();
        $retval['itemid'] = $this->itemid;
        $retval['itemtype'] = $this->itemtype;
        $retval['annotation'] = $this->annotation;
        $retval['bdata'] = $this->bdata;

	return $retval;
    }

}

?>
