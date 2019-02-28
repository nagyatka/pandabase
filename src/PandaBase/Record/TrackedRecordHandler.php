<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 16.
 * Time: 15:19
 */

namespace PandaBase\Record;


use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\Scheme\Table;
use PandaBase\Exception\DatabaseManagerNotExists;
use PandaBase\Exception\RecordValueNotExists;
use PandaBase\Exception\TableNotExists;

class TrackedRecordHandler extends RecordHandler{

    /**
     * Executes INSERT INTO operation and returns with the insert id.
     * @return int
     * @throws \Exception
     */
    public function insert(): int
    {
        $params = $this->databaseRecord->getAll();

        //Felesleges elemek törlése (seq_id,record_status,history,from, lazy_attr)
        unset($params[$this->tableDescriptor->get(Table::TABLE_SEQ_ID)]);
        unset($params[Table::HISTORY_FROM]);
        unset($params[Table::HISTORY_TO]);
        unset($params[Table::RECORD_STATUS]);
        foreach ($this->tableDescriptor->getAllLazyAttributeNames() as $attributeName) {
            unset($params[$attributeName]);
        }

        //Tartalmaz-e rec_status-t
        $containsTableId = array_key_exists($this->tableDescriptor->get(Table::TABLE_ID),$params);
        $params_key      = array_keys($params);

        if(!$containsTableId) {
            $result = ConnectionManager::getInstance()->getConnection()->fetchAssoc(
                "SELECT IFNULL(MAX(".$this->tableDescriptor->get(Table::TABLE_ID).") + 1, 1) as next_id FROM 
            ".$this->tableDescriptor->get(Table::TABLE_NAME).";");

            if($result == false) {
                throw new \Exception("Insert of new record is not possible he determination of next id have been failed");
            }

            $record_id = $result["next_id"];
        }
        else {
            $record_id = $params[$this->tableDescriptor->get(Table::TABLE_ID)];
        }

        //Lekérdezés összeállítása
        $insert_query   =   "INSERT INTO"." ".$this->tableDescriptor->get(Table::TABLE_NAME)." (";
        for($i = 0; $i < count($params_key); ++$i) {
            $insert_query.= "`".$params_key[$i]."`,";
        }

        if(!$containsTableId) {
            $insert_query.= " ".$this->tableDescriptor->get(Table::TABLE_ID).", ";
        }

        $insert_query.=  implode(", ",[Table::RECORD_STATUS, Table::HISTORY_FROM, Table::HISTORY_TO]).") VALUES (";

        for($i = 0; $i < count($params_key); ++$i) {
            $insert_query.= ":".$params_key[$i].",";
        }

        if(!$containsTableId) {
            $insert_query.= " ".$record_id.", ";
        }

        $insert_query   .=  "1, NOW(), '9999-12-31 00:00:00')";


        $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($insert_query);
        for($i = 0; $i < count($params); ++$i) {
            $prepared_statement->bindValue($params_key[$i],$params[$params_key[$i]]);
        }

        //Lekérdezés futtatása
        $prepared_statement->execute();

        return $record_id;
        /*
        //Ha tejesen új elem
        if(!$containsTableId) {
            $insert_id = ConnectionManager::getInstance()->getConnection()->lastInsertId();
            unset($prepared_statement);

            //UPDATE lekérdezés összeállítása
            $update_query = "UPDATE" . " " . $this->tableDescriptor->get(Table::TABLE_NAME) . " SET " . $this->tableDescriptor->get(Table::TABLE_ID) . "=:" . $this->tableDescriptor->get(Table::TABLE_ID) . " WHERE " . $this->tableDescriptor->get(Table::TABLE_SEQ_ID) . "=:" . $this->tableDescriptor->get(Table::TABLE_SEQ_ID);
            $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($update_query);
            $prepared_statement->bindValue($this->tableDescriptor->get(Table::TABLE_ID), $insert_id);
            $prepared_statement->bindValue($this->tableDescriptor->get(Table::TABLE_SEQ_ID), $insert_id);
            $prepared_statement->execute();
            unset($prepared_statement);

            return intval($insert_id);
        }
        //Ha már egy korábbi elemhez akarunk új rekordot adni akkor nincs más dolgunk
        else {
            return $params[$this->tableDescriptor->get(Table::TABLE_ID)];
        }
        */
    }

    /**
     * Returns with the record based on id parameter. If it doesn't exist returns with an empty array.
     * @param int $id
     * @return array
     */
    public function select(int $id): array
    {
        if($id < 0) {
            return [];
        }
        $select_query   = "SELECT * FROM"." ".$this->tableDescriptor->get(Table::TABLE_NAME)." WHERE ".Table::RECORD_STATUS." = 1 AND ".$this->tableDescriptor->get(Table::TABLE_ID)."=:".$this->tableDescriptor->get(Table::TABLE_ID);
        $params         = array(
            $this->tableDescriptor->get(Table::TABLE_ID) => $id
        );
        $result = ConnectionManager::getInstance()->getConnection()->fetchAssoc($select_query,$params);
        return $result == false ? array() : $result;
    }

    /**
     * @return mixed|void
     * @throws TableNotExists
     * @throws RecordValueNotExists
     */
    public function edit()
    {
        if(array_key_exists($this->tableDescriptor->get(Table::TABLE_ID),$this->databaseRecord->getAll())) {
            $this->remove();
            $this->insert();
        }
        else {
            throw new RecordValueNotExists("Table id not exists in array");
        }
    }

    /**
     * @return void
     * @throws DatabaseManagerNotExists
     * @throws TableNotExists
     * @throws RecordValueNotExists
     */
    public function remove()
    {
        $remove_query   = "UPDATE ".$this->tableDescriptor->get(Table::TABLE_NAME)." SET ".Table::RECORD_STATUS." = 0, ".
            Table::HISTORY_TO." = NOW() WHERE ".$this->tableDescriptor->get(Table::TABLE_ID)."=:".
            $this->tableDescriptor->get(Table::TABLE_ID)." AND ".Table::RECORD_STATUS."=1";
        $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($remove_query);
        $prepared_statement->bindValue($this->tableDescriptor->get(Table::TABLE_ID),$this->databaseRecord->get($this->tableDescriptor->get(Table::TABLE_ID)));
        $prepared_statement->execute();
    }

    public function list(string $column_name, $value): array
    {
        $select_query   = "SELECT * FROM ".$this->tableDescriptor->get(Table::TABLE_NAME)." WHERE ".Table::RECORD_STATUS." = 1 AND $column_name=:value";
        $params         = array(
            "value" => $value
        );
        $result = ConnectionManager::getInstance()->getConnection()->fetchAll($select_query,$params);
        return $result == false ? array() : $result;
    }
}