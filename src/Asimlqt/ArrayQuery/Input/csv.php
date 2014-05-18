<?php

namespace Asimlqt\ArrayQuery\Input;

class Csv implements Input
{
    const ROW_LENGTH = 10000;
    
    protected $data;
    
    /**
     * 
     * @param string  $filename  the full path to the file
     * @param boolean $hasHeader if true then the first row of the csv will be
     *                           used as the keys to the inner arrays
     * 
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    public function __construct($filename, $delimiter = ",", $hasHeader = true)
    {
        if(!file_exists($filename)) {
            throw new FileNotFoundException();
        }
        
        if(!is_readable($filename)) {
            throw new FileNotReadableException();
        }
        
        $this->data = array();
        $fh = fopen($filename, 'r');
        
        if($hasHeader) {
            $header = fgetcsv($fh, self::ROW_LENGTH, $delimiter);
        }
        
        while(($row = fgetcsv($fh, self::ROW_LENGTH, ",")) !== false) {
            if($hasHeader) {
                $this->data[] = array_combine($header, $row);
            } else {
                $this->data[] = $row;
            }
        }
    }
    
    public function getData()
    {
        return $this->data;
    }
}