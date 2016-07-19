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
            $sql = " AND (history_from BETWEEN :from_date AND :to_date) AND (history_to BETWEEN :from_date AND :to_date) ";
        }

        $sql .= "ORDER BY ".$tableDescriptor->get(TABLE_SEQ_ID)." DESC";

        return ConnectionManager::getInstance()->getInstanceRecords(self::class,
                $sql,[
                $tableDescriptor->get(TABLE_ID) => $this->get($tableDescriptor->get(TABLE_ID))
            ]);
    }

    /**
     * @param string $date Date string.
     * @return int
     * @throws \PandaBase\Exception\ConnectionNotExistsException
     * @throws \PandaBase\Exception\RecordValueNotExists
     * @throws \PandaBase\Exception\TableDescriptorNotExists
     */
    protected function getHistoryAtDateInternal($date) {
        $tableDescriptor = $this->getTableDescriptor();
        $sql = "SELECT ".$tableDescriptor->get(TABLE_ID)." FROM ".$tableDescriptor->get(TABLE_NAME)." 
            WHERE ".$tableDescriptor->get(TABLE_ID)."=:".$tableDescriptor->get(TABLE_ID)." AND :history_date >= history_from AND :history_date <= history_to";

        $result = ConnectionManager::getInstance()->getDefault()->fetchAssoc($sql,[
            $tableDescriptor->get(TABLE_ID) => $this->get($tableDescriptor->get(TABLE_ID)),
            "history_date"                  => $date
        ]);

        return intval($result[$tableDescriptor->get(TABLE_ID)]);
    }

} 