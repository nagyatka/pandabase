<?php

namespace PandaBase\Record;


use PandaBase\Connection\TableDescriptor;

class HistoryableRecord extends InstanceRecord {
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
            $simpleHandler = new HistoryAbleRecordHandler($this->getTableDescriptor());
            $simpleHandler->setManagedRecord($this);
            return $simpleHandler;
        } else {
            return new HistoryAbleRecordHandler($tableDescriptor);
        }
    }

} 