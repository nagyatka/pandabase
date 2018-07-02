<?php

namespace PandaBase\Record;


use PandaBase\AccessManagement\AccessibleObject;
use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\Scheme\Table;
use PandaBase\Exception\AccessDeniedException;

class TrackedRecord extends InstanceRecord {

    /**
     * @param Table $tableDescriptor
     * @return RecordHandler
     */
    public function getRecordHandler(Table $tableDescriptor = null): RecordHandler
    {
        if($tableDescriptor == null) {
            $simpleHandler = new TrackedRecordHandler($this->getTable());
            $simpleHandler->setManagedRecord($this);
            return $simpleHandler;
        } else {
            return new TrackedRecordHandler($tableDescriptor);
        }
    }

    /**
     * @return array
     */
    public function getHistory() {
        return $this->getHistoryBetweenDates();
    }

    /**
     * @param string $from
     * @param string $to
     * @return array
     * @throws \PandaBase\Exception\RecordValueNotExists
     * @throws \PandaBase\Exception\TableNotExists
     */
    public function getHistoryBetweenDates($from = null ,$to = null) {
        $tableDescriptor = $this->getTable();

        $sql =
            "SELECT * FROM ".$tableDescriptor->get(Table::TABLE_NAME)." ".
            "WHERE ".$tableDescriptor->get(Table::TABLE_ID)."=:".$tableDescriptor->get(Table::TABLE_ID);

        if($from != null && $to != null) {
            $sql = " AND (history_from BETWEEN :from_date AND :to_date) AND (history_to BETWEEN :from_date AND :to_date) ";
        }

        $sql .= "ORDER BY ".$tableDescriptor->get(Table::TABLE_SEQ_ID)." DESC";

        return ConnectionManager::getInstanceRecords(self::class,
                $sql,[
                $tableDescriptor->get(Table::TABLE_ID) => $this->get($tableDescriptor->get(Table::TABLE_ID))
            ]);
    }

    /**
     * @param string $date Date string.
     * @return int
     * @throws \PandaBase\Exception\ConnectionNotExistsException
     * @throws \PandaBase\Exception\RecordValueNotExists
     * @throws \PandaBase\Exception\TableNotExists
     */
    protected function getHistoryAtDateInternal($date) {
        $tableDescriptor = $this->getTable();
        $sql =
            "SELECT ".$tableDescriptor->get(Table::TABLE_ID)." ".
            "FROM ".  $tableDescriptor->get(Table::TABLE_NAME)." ".
            "WHERE ". $tableDescriptor->get(Table::TABLE_ID)."=:".$tableDescriptor->get(Table::TABLE_ID)." AND :history_date >= history_from AND :history_date <= history_to";

        $result = ConnectionManager::getInstance()
            ->getDefault()
            ->fetchAssoc($sql,[
                $tableDescriptor->get(Table::TABLE_ID)    => $this->get($tableDescriptor->get(Table::TABLE_ID)),
                "history_date"                                      => $date
            ]);

        return intval($result[$tableDescriptor->get(Table::TABLE_ID)]);
    }

} 