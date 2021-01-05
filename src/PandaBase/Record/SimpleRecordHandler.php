<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 16.
 * Time: 14:55
 */

namespace PandaBase\Record;


use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\Scheme\Table;

class SimpleRecordHandler extends RecordHandler {

    /**
     * @return int
     */
    public function insert(): int
    {
        $params = $this->databaseRecord->getAll();
        unset($params[$this->tableDescriptor->get(Table::TABLE_ID)]);
        foreach ($this->tableDescriptor->getAllLazyAttributeNames() as $attributeName) {
            unset($params[$attributeName]);
        }
        $params_key     =   array_keys($params);

        //Lekérdezés összeállítása
        $insert_query   =   "INSERT INTO"." ".$this->tableDescriptor->get(Table::TABLE_NAME)." (";
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
    public function select(int $id)
    {
        if($id < 0) {
            return array();
        }
        $select_query   = "SELECT * FROM"." ".$this->tableDescriptor->get(Table::TABLE_NAME)." WHERE ".$this->tableDescriptor->get(Table::TABLE_ID)."=:".$this->tableDescriptor->get(Table::TABLE_ID);
        $params         = array(
            $this->tableDescriptor->get(Table::TABLE_ID) => $id
        );
        $result = ConnectionManager::getInstance()->getConnection()->fetchAssoc($select_query,$params);
        return $result == false ? array() : $result;
    }

    /**
     * @return mixed
     */
    public function edit()
    {
        if(array_key_exists($this->tableDescriptor->get(Table::TABLE_ID),$this->databaseRecord->getAll())) {
            // Ha nem változott érték akkor nem kell semmit csinálni
            if(count($this->databaseRecord->getChangedKeys()) < 1) {
                return true;
            }

            //Ki kell szedni az id értéket, hogy az ne kerüljön bele SET részbe
            $id = $this->databaseRecord->get($this->databaseRecord->getTable()->get(Table::TABLE_ID));
            $params = $this->databaseRecord->getAll();
            unset($params[$this->databaseRecord->getTable()->get(Table::TABLE_ID)]);
            foreach ($this->tableDescriptor->getAllLazyAttributeNames() as $attributeName) {
                unset($params[$attributeName]);
            }

            // Leszűrünk csak azokra a mezőkre amelyek tényleg változtak
            $filtered_params = [];
            foreach ($this->databaseRecord->getChangedKeys() as $changedKey) {
                $filtered_params[$changedKey] = $params[$changedKey];
            }
            $params = $filtered_params;

            $params_key = array_keys($params);

            $sql = "UPDATE ".$this->databaseRecord->getTable()->get(Table::TABLE_NAME)." SET ";
            for($i = 0; $i < count($params_key); ++$i) {
                $sql.= $params_key[$i]."= :".$params_key[$i];
                if($i != count($params_key)-1) $sql.=",";
            }
            $sql.= " WHERE ".$this->databaseRecord->getTable()->get(Table::TABLE_ID)."=:".$this->databaseRecord->getTable()->get(Table::TABLE_ID);
            //Visszatesszük az értéket
            $params[$this->databaseRecord->getTable()->get(Table::TABLE_ID)] = $id;
            unset($params_key);
            $params_key = array_keys($params);

            $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($sql);
            for($i = 0; $i < count($params); ++$i) {
                $prepared_statement->bindValue($params_key[$i],$params[$params_key[$i]]);
            }

            //Lekérdezés futtatása
            $prepared_statement->execute();

            $this->databaseRecord->resetChangedKeys();
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @return void
     */
    public function remove()
    {
        $sql = "DELETE FROM"." ".$this->databaseRecord->getTable()->get(Table::TABLE_NAME)."
                WHERE ".$this->databaseRecord->getTable()->get(Table::TABLE_ID)."= :".$this->databaseRecord->getTable()->get(Table::TABLE_ID);
        $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($sql);
        $prepared_statement->bindValue($this->databaseRecord->getTable()->get(Table::TABLE_ID),$this->databaseRecord->get($this->databaseRecord->getTable()->get(Table::TABLE_ID)));
        $prepared_statement->execute();
    }

    /**
     * @param string $column_name
     * @param mixed $value
     * @return array
     */
    public function list(string $column_name, $value): array
    {
        $select_query   = "SELECT * FROM ".$this->tableDescriptor->get(Table::TABLE_NAME)." WHERE $column_name=:value";
        $params         = array(
            "value" => $value
        );
        $result = ConnectionManager::getInstance()->getConnection()->fetchAll($select_query,$params);
        return $result == false ? array() : $result;
    }
}