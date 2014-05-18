<?php

namespace Asimlqt\ArrayQuery;

use Asimlqt\ArrayQuery\Input\Input;

/**
 * Description of Query
 *
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
class Query
{    
    /**
     * queried data
     *
     * @var array
     */
    protected $queried = array();
    
    /**
     *
     * @var Asimlqt\ArrayQuery\Input\Input
     */
    protected $source;
    
    /**
     * 
     * @param \Asimlqt\ArrayQuery\Input\Input $source
     */
    public function __construct(Input $source)
    {
        $this->source = $source;
        $this->reset();
    }

    /**
     * Resets the query. Can be used to run more than one query on the same
     * data set.
     */
    public function reset()
    {
        $this->queried = $this->source->getData();
    }
    
    /**
     * 
     * @return array
     */
    public function getResult()
    {
        return $this->queried;
    }
    
    /**
     * Select columns from the report data. This will remove the other columns
     * immediately so they can't be used in any subsequent calls.
     * 
     * @param type $columns
     * @return \Asimlqt\Piq\Piq
     */
    public function select($columns)
    {
        $cols = array_flip($columns);
        foreach($this->queried as $index => $row) {
            $this->queried[$index] = array_intersect_key($row, $cols);
        }
        
        return $this;
    }
    
    /**
     * Remove any rows which don't have any value from $values in $column
     * 
     * @param string $column
     * @param array  $values
     * 
     * @return \Asimlqt\Piq\Piq
     */
    public function filter($column, array $values)
    {
        foreach($this->queried as $index => $row) {
            if(!isset($row[$column]) || !in_array($row[$column], $values)) {
                unset($this->queried[$index]);
            }
        }
        
        return $this;
    }
    
    /**
     * 
     * @param callback $closure
     * 
     * @return \Asimlqt\Piq\Piq
     */
    public function where($closure)
    {
        foreach($this->queried as $index => $row) {
            if(call_user_func($closure, $row) !== true) {
                unset($this->queried[$index]);
            }
        }
        
        return $this;
    }
    
    /**
     * 
     * @param array $groupByColumns
     * @param array $sumColumns
     * 
     * @return \Asimlqt\Piq\Piq
     */
    public function groupAndSum($groupByColumns, $sumColumns)
    {
        $this->groupBy($groupByColumns, function($row, $nextRow) use ($sumColumns) {
            foreach(array_keys($sumColumns) as $col) {
                $row[$col] += (float) $nextRow[$col];
            }
            return $row;
        });
        
        foreach($this->queried as &$row) {
            foreach($sumColumns as $col => $precision) {
                $row[$col] = round($row[$col], $precision);
            }
        }
        
        return $this;
    }
    
    /**
     * 
     * @param array    $columns
     * @param callback $closure
     * 
     * @return \Asimlqt\Piq\Piq
     */
    public function groupBy($columns, $closure = null)
    {
        $result = array();
        $callable = is_callable($closure);
        
        foreach($this->queried as $row) {
            $hashItems = array();
            foreach($columns as $col) {
                $hashItems[] = $row[$col];
            }
            $hash = implode('::', $hashItems);
            if(isset($result[$hash])) {
                if($callable) {
                    $result[$hash] = call_user_func_array($closure, array($result[$hash], $row));
                }
            } else {
                $result[$hash] = $row;
            }
        }
        
        $this->queried = array_values($result);
        return $this;
    }
    
    /**
     * 
     * @param array $groupByColumns
     * @param string $maxColumn
     * 
     * @return \Asimlqt\Piq\Piq
     */
    public function groupByMax($groupByColumns, $maxColumn)
    {
        return $this->groupByMinMax($groupByColumns, $maxColumn, "max");
    }

    /**
     * 
     * @param array $groupByColumns
     * @param string $minColumn
     * 
     * @return \Asimlqt\Piq\Piq
     */
    public function groupByMin($groupByColumns, $minColumn)
    {
        return $this->groupByMinMax($groupByColumns, $minColumn, "min");
    }
    
    /**
     * 
     * @param type $groupByColumns
     * @param type $minMaxColumn
     * @param string $type
     * 
     * @return \Asimlqt\Piq\Piq
     */
    protected function groupByMinMax($groupByColumns, $minMaxColumn, $type)
    {
        $this->groupBy($groupByColumns, function($row, $nextRow) use ($minMaxColumn, $type) {
            if($type === "max" && $nextRow[$minMaxColumn] > $row[$minMaxColumn]) {
                return $nextRow;
            }
            else if($type === "min" && $nextRow[$minMaxColumn] < $row[$minMaxColumn]) {
                return $nextRow;
            }
            return $row;
        });
                
        return $this;
    }
    
    /**
     * 
     * @param type $column
     * 
     * @return \Asimlqt\Piq\Piq
     */
    public function max($column)
    {
        $maxIndex = -1;
        $maxValue = null;
        foreach($this->queried as $index => $row) {
            $value = (float) $row[$column];
            if($maxValue === null || ($value = (float) $row[$column] > $maxValue)) {
                $maxValue = $value;
                $maxIndex = $index;
            }
        }
        
        if($maxIndex !== -1) {
            $this->queried = $this->queried[$maxIndex];
        }
        
        return $this;
    }
    
    /**
     * 
     * @param array $order
     * 
     * @return \Asimlqt\Piq\Piq
     */
    public function orderBy(array $order)
    {
        $funcArgs = array();
        foreach(array_keys($order) as $column) {
            $funcArgs[$column] = array();
        }
        
        foreach($this->queried as $index => $row) {
            foreach($order as $column => $sortOrder) {
                $funcArgs[$column][$index] = $row[$column];
            }
        }
        
        $args = array();
        foreach($order as $column => $sortOrder) {
            $args[] = $funcArgs[$column];
            $args[] = $sortOrder;
        }
        $args[] = &$this->queried;
        
        call_user_func_array("array_multisort", $args);
        return $this;
    }
}