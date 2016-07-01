<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 16.
 * Time: 15:19
 */

namespace PandaBase\Record;


use PandaBase\Connection\ConnectionManager;
use PandaBase\Exception\DatabaseManagerNotExists;
use PandaBase\Exception\RecordValueNotExists;
use PandaBase\Exception\TableDescriptorNotExists;

class HistoryAbleRecordHandler extends RecordHandler{

    /**
     * @return int
     */
    public function insert()
    {
        $params = $this->databaseRecord->getAll();
        //Felesleges elemek törlése (seq_id,record_status,history,from)
        unset($params[$this->tableDescriptor->get(TableDescriptor::TABLE_SEQ_ID)]);
        unset($params["history_from"]);

        //Tartalmaz-e rec_status-t
        $containsTableId = array_key_exists($this->tableDescriptor->get(TableDescriptor::TABLE_ID),$params);
        $params_key      = array_keys($params);

        //Lekérdezés összeállítása
        $insert_query   =   "INSERT INTO"." ".$this->tableDescriptor->get(TableDescriptor::TABLE_NAME)." (";
        for($i = 0; $i < count($params_key); ++$i) {
            $insert_query.= "`".$params_key[$i]."`,";
        }
        $insert_query.="record_status,history_from) VALUES (";

        for($i = 0; $i < count($params_key); ++$i) {
            $insert_query.= ":".$params_key[$i].",";
        }
        $insert_query   .=  "1,NOW())";


        $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($insert_query);
        for($i = 0; $i < count($params); ++$i) {
            $prepared_statement->bindValue($params_key[$i],$params[$params_key[$i]]);
        }

        //Lekérdezés futtatása
        $prepared_statement->execute();

        //Ha tejesen új elem
        if(!$containsTableId) {
            $insert_id = ConnectionManager::getInstance()->getConnection()->lastInsertId();
            unset($prepared_statement);

            //UPDATE lekérdezés összeállítása
            $update_query = "UPDATE" . " " . $this->tableDescriptor->get(TableDescriptor::TABLE_NAME) . " SET " . $this->tableDescriptor->get(TableDescriptor::TABLE_ID) . "=:" . $this->tableDescriptor->get(TableDescriptor::TABLE_ID) . " WHERE " . $this->tableDescriptor->get(TableDescriptor::TABLE_SEQ_ID) . "=:" . $this->tableDescriptor->get(TableDescriptor::TABLE_SEQ_ID);
            $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($update_query);
            $prepared_statement->bindValue($this->tableDescriptor->get(TableDescriptor::TABLE_ID), $insert_id);
            $prepared_statement->bindValue($this->tableDescriptor->get(TableDescriptor::TABLE_SEQ_ID), $insert_id);
            $prepared_statement->execute();
            unset($prepared_statement);

            return intval($insert_id);
        }
        //Ha már egy korábbi elemhez akarunk új rekordot adni akkor nincs más dolgunk
        else {
            return $params[$this->tableDescriptor->get(TableDescriptor::TABLE_ID)];
        }
    }

    /**
     * @param int $id
     * @return array
     */
    public function select($id)
    {
        $select_query   = "SELECT * FROM"." ".$this->tableDescriptor->get(TableDescriptor::TABLE_NAME)." WHERE record_status = 1 AND ".$this->tableDescriptor->get(TableDescriptor::TABLE_ID)."=:".$this->tableDescriptor->get(TableDescriptor::TABLE_ID);
        $params         = array(
            $this->tableDescriptor->get(TableDescriptor::TABLE_ID) => $id
        );
        $result = ConnectionManager::getInstance()->getConnection()->fetchAssoc($select_query,$params);
        return $result == false ? array() : $result;
    }

    /**
     * @return mixed|void
     * @throws TableDescriptorNotExists
     * @throws RecordValueNotExists
     */
    public function edit()
    {
        if(array_key_exists($this->tableDescriptor->get(TableDescriptor::TABLE_ID),$this->databaseRecord->getAll())) {
            $this->remove();
            $this->insert($this->databaseRecord->getAll());
        }
        else {
            throw new RecordValueNotExists("Table id not exists in array");
        }
    }

    /**
     * @return void
     * @throws DatabaseManagerNotExists
     * @throws TableDescriptorNotExists
     * @throws RecordValueNotExists
     */
    public function remove()
    {
        $remove_query   = "UPDATE ".$this->tableDescriptor->get(TableDescriptor::TABLE_NAME)." SET rec_status = 0, history_to = NOW() WHERE ".$this->tableDescriptor->get(TableDescriptor::TABLE_ID)."=:".$this->tableDescriptor->get(TableDescriptor::TABLE_ID)." AND rec_status=1";
        $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($remove_query);
        $prepared_statement->bindValue($this->tableDescriptor->get(TableDescriptor::TABLE_ID),$this->databaseRecord->get($this->tableDescriptor->get(TableDescriptor::TABLE_ID)));
        $prepared_statement->execute();
    }
}