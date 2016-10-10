<?php

namespace PandaBase\Record;


use PandaBase\AccessManagement\AccessibleObject;
use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\TableDescriptor;
use PandaBase\Exception\AccessDeniedException;
use PandaBase\Exception\RecordValueNotExists;
use PandaBase\Exception\TableDescriptorNotExists;

abstract class DatabaseRecord implements \ArrayAccess {

    /**
     * @var array
     */
    protected $values;

    /**
     * @var TableDescriptor
     */
    private $tableDescriptor;

    /**
     * DatabaseRecord constructor.
     * @param TableDescriptor $tableDescriptor
     * @param $values
     */
    function __construct(TableDescriptor $tableDescriptor, $values)
    {
        $this->tableDescriptor = $tableDescriptor;
        $this->values = $values;
    }

    /**
     * @return TableDescriptor
     */
    public function getTableDescriptor()
    {
        return $this->tableDescriptor;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public abstract function set($key,$value);

    /**
     * @param $params
     * @return $this
     */
    public function setAll($params)
    {
        foreach ($params as $key => $value) {
            $this->set($key,$value);
        }
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     * @throws AccessDeniedException
     */
    public function get($key) {
        if(!isset($this->values[$key])) {
            //throw new RecordValueNotExists("Value ".$key." not exists in ".$this->getTableDescriptor()->get(TableDescriptor::TABLE_NAME));
            return null;
        }

        // Ha van beállítva jogosultság, akkor ellenőrizni kell
        if(in_array(AccessibleObject::class,class_uses($this))) {
            /** @var AccessibleObject $object */
            $object = $this;
            if(!ConnectionManager::getInstance()->getAccessManager()->checkReadAccess($object)) {
                throw new AccessDeniedException;
            }
        }

        return $this->values[$key];
    }

    /**
     * @return array
     */
    public function getAll() {
        return $this->values;
    }

    /**
     * @param TableDescriptor $tableDescriptor
     * @return RecordHandler
     */
    public abstract function getRecordHandler(TableDescriptor $tableDescriptor = null);

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
        return array_key_exists($offset,$this->values);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset)) {
            return null;
        }
        return $this->values[$offset];
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
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset,$value);
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
        $this->set($offset,null);
    }
}