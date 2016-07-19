<?php

namespace PandaBase\Record;


use Exception;
use PandaBase\Connection\ConnectionConfiguration;
use PandaBase\Connection\ConnectionManager;
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


    /**
     * @return InstanceRecordContainer
     */
    public function getHistory() {
        return $this->getHistoryBetweenDates();
    }

    /**
     * @param null $from
     * @param null $to
     * @return InstanceRecordContainer
     * @throws \PandaBase\Exception\RecordValueNotExists
     * @throws \PandaBase\Exception\TableDescriptorNotExists
     */
    public function getHistoryBetweenDates($from = null ,$to = null) {
        $tableDescriptor = $this->getTableDescriptor();

        $sql = "SELECT * FROM ".$tableDescriptor->get(TABLE_NAME)." 
            WHERE ".$tableDescriptor->get(TABLE_ID)."=:".$tableDescriptor->get(TABLE_ID);

        if($from != null && $to != null) {
            $sql = " AND (history_from BETWEEN :from_date AND :to_date) AND ((history_to BETWEEN :from_date AND :to_date) OR history_to IS NULL) ";
        }

        $sql .= "ORDER BY ".$tableDescriptor->get(TABLE_SEQ_ID)." DESC";

        return ConnectionManager::getInstance()->getInstanceRecords(self::class,
                $sql,[
                $tableDescriptor->get(TABLE_ID) => $this->get($tableDescriptor->get(TABLE_ID))
            ]);
    }


    public function getHistoryAtDate($date) {
        //TODO: Implement

        throw new Exception("Not yet implemented.");
    }

} 