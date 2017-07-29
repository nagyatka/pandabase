<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 16.
 * Time: 14:50
 */

namespace PandaBase\Record;


use PandaBase\AccessManagement\AccessibleObject;
use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\Scheme\Table;
use PandaBase\Exception\AccessDeniedException;

class SimpleRecord extends InstanceRecord {

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
     * @param Table $tableDescriptor
     * @return RecordHandler
     */
    public function getRecordHandler(Table $tableDescriptor = null): RecordHandler
    {
        if($tableDescriptor == null) {
            $simpleHandler = new SimpleRecordHandler($this->getTable());
            $simpleHandler->setManagedRecord($this);
            return $simpleHandler;
        } else {
            return new SimpleRecordHandler($tableDescriptor);
        }
    }

}