<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 16.
 * Time: 14:55
 */

namespace PandaBase\Record;


use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\TableDescriptor;

class SimpleRecordHandler extends RecordHandler {

    /**
     * @return array
     */
    public function insert()
    {
        $params = $this->databaseRecord->getAll();
        unset($params[$this->tableDescriptor->get(TableDescriptor::TABLE_ID)]);
        $params_key     =   array_keys($params);

        //Lekérdezés összeállítása
        $insert_query   =   "INSERT INTO"." ".$this->tableDescriptor->get(TableDescriptor::TABLE_NAME)." (";
        for($i = 0; $i < count($params_key); ++$i) {
            $insert_query.= $params_key[$i];
            if($i < (count($params_key)-1) ) $insert_query.=",";
        }
        $insert_query .= ") VALUES (";

        for($i = 0; $i < count($params_key); ++$i) {
            $insert_query.= ":".$params_key[$i];
            if($i < (count($params_key)-1) ) $insert_query.=",";
        }
        $insert_query.= ")";

        $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($insert_query);
        for($i = 0; $i < count($params); ++$i) {
            $prepared_statement->bindValue($params_key[$i],$params[$params_key[$i]]);
        }

        //Lekérdezés futtatása
        $prepared_statement->execute();
        $insert_id = ConnectionManager::getInstance()->getConnection()->lastInsertId();
        unset($prepared_statement);

        return intval($insert_id);
    }


    /**
     * @param int $id
     * @return array
     */
    public function select($id)
    {
        if($id == 0) {
            return array();
        }
        $select_query   = "SELECT * FROM"." ".$this->tableDescriptor->get(TableDescriptor::TABLE_NAME)." WHERE ".$this->tableDescriptor->get(TableDescriptor::TABLE_ID)."=:".$this->tableDescriptor->get(TableDescriptor::TABLE_ID);
        $params         = array(
            $this->tableDescriptor->get(TableDescriptor::TABLE_ID) => $id
        );
        $result = ConnectionManager::getInstance()->getConnection()->fetchAssoc($select_query,$params);
        return $result == false ? array() : $result;
    }

    public function edit()
    {
        if(array_key_exists($this->tableDescriptor->get(TABLE_ID),$this->databaseRecord->getAll())) {
            //Ki kell szedni az id értéket, hogy az ne kerüljön bele SET részbe
            $id = $this->databaseRecord->get($this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_ID));
            $params = $this->databaseRecord->getAll();
            unset($params[$this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_ID)]);

            $params_key =   array_keys($params);

            $sql = "UPDATE ".$this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_NAME)." SET ";
            for($i = 0; $i < count($params_key); ++$i) {
                $sql.= $params_key[$i]."= :".$params_key[$i];
                if($i != count($params_key)-1) $sql.=",";
            }
            $sql.= " WHERE ".$this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_ID)."=:".$this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_ID);
            //Visszatesszük az értéket
            $params[$this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_ID)] = $id;
            unset($params_key);
            $params_key =   array_keys($params);

            $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($sql);
            for($i = 0; $i < count($params); ++$i) {
                $prepared_statement->bindValue($params_key[$i],$params[$params_key[$i]]);
            }

            //Lekérdezés futtatása
            $prepared_statement->execute();
            return true;
        }
        else {
            return false;
        }
    }

    public function remove()
    {
        $sql = "DELETE FROM"." ".$this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_NAME)."
                WHERE ".$this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_ID)."= :".$this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_ID);
        $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($sql);
        $prepared_statement->bindValue($this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_ID),$this->databaseRecord->get($this->databaseRecord->getTableDescriptor()->get(TableDescriptor::TABLE_ID)));
        $prepared_statement->execute();
    }


}