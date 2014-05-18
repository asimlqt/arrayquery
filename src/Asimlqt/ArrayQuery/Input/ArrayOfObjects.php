<?php

namespace Asimlqt\ArrayQuery\Input;

class ArrayOfObjects implements Input
{
    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = array();
        foreach($data as $row) {
            $this->data[] = (array) $row;
        }
    }
    
    public function getData()
    {
        return $this->data;
    }
}