<?php

class Cell {
    function __construct($id, $field, $result = null) {
        $this->id = $id;
        $this->field = $field;
        $this->result = $result;
    }
}