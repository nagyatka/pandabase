<?php

namespace PandaBase\Record;


use PandaBase\Exception\NotDatabaseRecordInstanceException;
use PandaBase\Exception\RecordValueNotExists;

class DatabaseRecordContainer implements \ArrayAccess, \Countable {

    /**
     * @var DatabaseRecord[]
     */
    private $records;

    /**
     * @param DatabaseRecord[] $records
     */
    function __construct($records)
    {
        $this->records = $records;
    }

    public function size() {
        return count($this->records);
    }

    public function foreachRecords($func) {
        foreach ($this->records as $record) {
            $func($record);
        }
        return $this;
    }

    public function filter($func) {
        $newRecords = array();
        foreach ($this->records as $record) {
            if($func($record) == true) $newRecords[] = $record;
        }
        return new DatabaseRecordContainer($newRecords);
    }

    public function map($func) {
        $newRecords = array();
        foreach ($this->records as $record) {
            $newRecords[] = $func($record);
        }
        return new DatabaseRecordContainer($newRecords);
    }

    public function reduce($func) {
        $temp = null;
        for($i = 1; $i < count($this->records); ++$i) {
            if($temp == null) {
                $temp = $func($this->records[0],$this->records[1]);
            } else {
                $temp = $func($temp,$this->records[$i]);
            }
        }
        return $temp;

    }

    public function findBy($key,$value) {
        foreach ($this->records as $record) {
            if($record[$key] == $value) return $record;
        }
        return null;
    }

    public function get($index) {
        return $this->records[$index];
    }

    public function getAll($key) {
        $res = array();
        $this->foreachRecords(function($record) use(&$res,$key) {
           $res[] = $record[$key];
        });
        return $res;
    }

    public function sortBy($key,$order) {
        $res = $this->records;
        array_multisort($this->getAll($key), $order, $res);
        return new DatabaseRecordContainer($res);
    }

    public function join($key,DatabaseRecordContainer $container) {
        $newRecords = array();
        $this->foreachRecords(function(DatabaseRecord $record1) use ($container,&$newRecords,$key){
            $container->foreachRecords(function(DatabaseRecord $record2) use($record1,&$newRecords,$key){
                if($record1[$key] == $record2[$key]) $newRecords[] = new MixedRecord($record1->setAll($record2->getAll())->getAll());
            });
        });
        return new DatabaseRecordContainer($newRecords);
    }


    public function leftJoin($key,DatabaseRecordContainer $container) {
        $newRecords = array();
        $this->foreachRecords(function(DatabaseRecord $record1) use ($container,&$newRecords,$key){
            $foundMatch = false;
            $container->foreachRecords(function(DatabaseRecord $record2) use($record1,&$newRecords,$key,&$foundMatch){
                if($record1[$key] == $record2[$key]){
                    $foundMatch = true;
                    $newRecords[] = new MixedRecord($record1->setAll($record2->getAll())->getAll());
                }
            });
            if(!$foundMatch) {
                $row = array_fill_keys(array_merge(array_keys($record1->getAll()),array_keys($container[0]->getAll())),null);
                $newRecord = new MixedRecord($row);
                $newRecords[] = $newRecord->setAll($record1->getAll());
            }
        });
        return new DatabaseRecordContainer($newRecords);
    }



    public function rightJoin($key,DatabaseRecordContainer $container) {
        $newRecords = array();
        $_this = $this;
        $container->foreachRecords(function(DatabaseRecord $record1) use ($_this,&$newRecords,$key){
            $foundMatch = false;
            $_this->foreachRecords(function(DatabaseRecord $record2) use($record1,&$newRecords,$key,&$foundMatch){
                if($record1[$key] == $record2[$key]){
                    $foundMatch = true;
                    $newRecords[] = new MixedRecord($record1->setAll($record2->getAll())->getAll());
                }
            });
            if(!$foundMatch) {
                $row = array_fill_keys(array_merge(array_keys($record1->getAll()),array_keys($_this[0]->getAll())),null);
                $newRecord = new MixedRecord($row);
                $newRecords[] = $newRecord->setAll($record1->getAll());
            }
        });
        return new DatabaseRecordContainer($newRecords);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->records[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @throws RecordValueNotExists
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset)) {
            throw new RecordValueNotExists();
        }
        return $this->records[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @throws NotDatabaseRecordInstanceException
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof DatabaseRecord) {
            $this->records[$offset] = $value;
        } else {
            throw new NotDatabaseRecordInstanceException();
        }

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->records[$offset] = null;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->size();
    }

    /**
     * @return DatabaseRecord[]
     */
    public function getRecords() {
        return $this->records;
    }
}