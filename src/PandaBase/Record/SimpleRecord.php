<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 16.
 * Time: 14:50
 */

namespace PandaBase\Record;


use PandaBase\Connection\TableDescriptor;

class SimpleRecord extends InstanceRecord {

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * @param TableDescriptor $tableDescriptor
     * @return RecordHandler
     */
    public function getRecordHandler(TableDescriptor $tableDescriptor = null)
    {
        if($tableDescriptor == null) {
            $simpleHandler = new SimpleRecordHandler($this->getTableDescriptor());
            $simpleHandler->setManagedRecord($this);
            return $simpleHandler;
        } else {
            return new SimpleRecordHandler($tableDescriptor);
        }
    }

}