<?php

namespace Asimlqt\ArrayQuery\Input;

class ArrayOfArrays implements Input
{
    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function getData()
    {
        return $this->data;
    }
}