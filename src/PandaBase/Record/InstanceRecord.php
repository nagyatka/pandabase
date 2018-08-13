<?php

namespace PandaBase\Record;

use function Couchbase\defaultDecoder;
use PandaBase\AccessManagement\AccessibleObject;
use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\Scheme\LazyAttribute;
use PandaBase\Connection\Scheme\Table;
use PandaBase\Exception\AccessDeniedException;

/**
 * Class InstanceRecord
 * @package PandaBase\Record
 */
abstract class InstanceRecord implements \ArrayAccess {

    /**
     * @var array
     */
    protected $values;

    /**
     * @var Table
     */
    private $table;

    /**
     * InstanceRecord constructor.
     *
     * @param integer|array $argument
     */
    function __construct($argument = null) {
        $this->table = ConnectionManager::getTable(get_class($this));
        // If the argument contains an id, we try to load the appropriate record from the table
        if(is_int($argument) || is_numeric($argument)) {
            $this->values = $this->getRecordHandler($this->table)->select(intval($argument));
        }
        // If the argument is an array, we suppose that it contains the columns of a record from the table
        // We have to highlight if the array contains the TableDescriptor::TABLE_ID we suppose the record
        // is already in table (UPDATE) otherwise we will insert it as a new record (INSERT INTO).
        elseif (is_array($argument)) {
            $this->values = $argument;
        }
        // We interpret argument as a new empty record
        else {
            $this->values = [];
        }
    }

    /**
     * It checks that the instance is a valid record from database.
     *
     * @return bool
     */
    public function isValid(): bool {
        return count($this->values) == 0 ? false : true;
    }

    /**
     * Removes the instance from database.
     */
    public function remove() {
        $recordHandler = $this->getRecordHandler();
        $recordHandler->setManagedRecord($this);
        $recordHandler->remove();
    }

    /**
     * Checks that the instance is already added (returns with true) to the database or not (returns with false).
     *
     * @return bool
     * @throws \PandaBase\Exception\TableNotExists
     */
    public function isNewInstance(): bool {
        /*
         * Checks the existence of the TABLE_ID on the instance. If it does not exist, it will interpret it as a new
         * instance and returns with true. Otherwise the return value is false.
         */
        return !isset($this->getAll()[$this->getTable()->get(Table::TABLE_ID)]);
    }

    /**
     *
     * @param string $key
     * @return mixed
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function get(string $key)
    {
        // Check permissions
        if(in_array(AccessibleObject::class,class_uses($this))) {
            /** @var AccessibleObject $object */
            $object = $this;
            if(!ConnectionManager::getInstance()->getAccessManager()->checkReadAccess($object)) {
                throw new AccessDeniedException;
            }
        }
        // Check lazy load
        $descriptor = ConnectionManager::getTable(get_class($this));
        if($descriptor->isLazyAttribute($key) && !isset($this->values[$key])) {
            $lazy  = $descriptor->getLazyAttribute($key);
            $class = $lazy->getClass();
            $type  = $lazy->getType();
            // We have to set value this way, because the write access is not guaranteed via 'set' function call
            switch ($type) {
                case LazyAttribute::OneToOne:
                    $this->values[$key] = new $class($this[$lazy->getKey()]);
                    break;
                case LazyAttribute::OneToMany:
                    $query_result = (new $class())->getRecordHandler()->list($lazy->getKey(), $this[$descriptor->get(Table::TABLE_ID)]);
                    $records = array();
                    foreach ($query_result as $result) {
                        $records[] = new $class($result);
                    }
                    $this->values[$key] = $records;
                    break;
                default:
                    throw new \InvalidArgumentException("Unknown LazyAttribute type (".$type.") for class ".$class);
            }
        }

        if(!isset($this->values[$key])) {
            return null;
        }
        return $this->values[$key];
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @param $key
     * @param $value
     * @throws AccessDeniedException
     */
    public function set($key, $value)
    {
        // Ha van beállítva jogosultság, akkor ellenőrizni kell
        if(in_array(AccessibleObject::class,class_uses($this))) {
            /** @var AccessibleObject $object */
            $object = $this;
            if(!ConnectionManager::getInstance()->getAccessManager()->checkWriteAccess($object)) {
                throw new AccessDeniedException;
            }
        }
        $this->values[$key] = $value;
    }

    /**
     * @param $params
     * @return void
     */
    public function setAll($params)
    {
        foreach ($params as $key => $value) {
            $this->set($key,$value);
        }
    }

    /**
     * @return array
     * @throws AccessDeniedException
     */
    public function getAll(): array {
        // Ha van beállítva jogosultság, akkor ellenőrizni kell
        if(in_array(AccessibleObject::class,class_uses($this))) {
            /** @var AccessibleObject $object */
            $object = $this;
            if(!ConnectionManager::getInstance()->getAccessManager()->checkReadAccess($object)) {
                throw new AccessDeniedException;
            }
        }
        return $this->values;
    }

    /**
     * @param Table $tableDescriptor
     * @return RecordHandler
     */
    public abstract function getRecordHandler(Table $tableDescriptor = null): RecordHandler;

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
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset,$this->values) || $this->table->isLazyAttribute($offset);
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
        return $this->get($offset);
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