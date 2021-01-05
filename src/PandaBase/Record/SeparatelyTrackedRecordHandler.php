<?php

namespace PandaBase\Record;


use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\Scheme\Table;

class SeparatelyTrackedRecordHandler extends RecordHandler
{

    const INSERT_NEW = "new";
    const INSERT_HIST= "history";

    private function clean_record($params) : array
    {
        unset($params[$this->tableDescriptor->get(Table::TABLE_ID)]);
        foreach ($this->tableDescriptor->getAllLazyAttributeNames() as $attributeName) {
            unset($params[$attributeName]);
        }
        return $params;
    }

    public function insert(): int
    {
        $params = $this->clean_record($this->databaseRecord->getAll());
        return $this->_insert($params);
    }

    public function select(int $id)
    {
        if($id < 0) {
            return array();
        }
        $select_query   = "SELECT * FROM"." ".$this->tableDescriptor->get(Table::TABLE_NAME)." WHERE ".
            $this->tableDescriptor->get(Table::TABLE_ID)."=:".$this->tableDescriptor->get(Table::TABLE_ID);
        $params         = array(
            $this->tableDescriptor->get(Table::TABLE_ID) => $id
        );
        $result = ConnectionManager::getInstance()->getConnection()->fetchAssoc($select_query,$params);
        return $result == false ? array() : $result;
    }

    public function list(string $column_name, $value): array
    {
        $select_query   = "SELECT * FROM ".$this->tableDescriptor->get(Table::TABLE_NAME)." WHERE $column_name=:value";
        $params         = array(
            "value" => $value
        );
        $result = ConnectionManager::getInstance()->getConnection()->fetchAll($select_query,$params);
        return $result == false ? array() : $result;
    }

    public function edit()
    {
        if(array_key_exists($this->tableDescriptor->get(Table::TABLE_ID),$this->databaseRecord->getAll())) {
            // Ha nem változott érték akkor nem kell semmit csinálni
            if(count($this->databaseRecord->getChangedKeys()) < 1) {
                return true;
            }

            //Ki kell szedni az id értéket, hogy az ne kerüljön bele SET részbe
            $id = $this->databaseRecord->get($this->databaseRecord->getTable()->get(Table::TABLE_ID));

            /*
             * Backup last state of the record (to the history table)
             */
            $this->backup($id);

            /*
             * Update the state with the new values
             */
            $params = $this->clean_record($this->databaseRecord->getAll());
            $params[Table::HISTORY_FROM] = date('Y-m-d H:i:s');
            $params_key =   array_keys($params);
            $sql = "UPDATE ".$this->databaseRecord->getTable()->get(Table::TABLE_NAME)." SET ";
            for($i = 0; $i < count($params_key); ++$i) {
                $sql.= $params_key[$i]."= :".$params_key[$i];
                if($i != count($params_key)-1) $sql.=",";
            }
            $sql.= " WHERE ".$this->databaseRecord->getTable()->get(Table::TABLE_ID)."=:".
                $this->databaseRecord->getTable()->get(Table::TABLE_ID);
            //Visszatesszük az értéket
            $params[$this->databaseRecord->getTable()->get(Table::TABLE_ID)] = $id;
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
        $id = $this->databaseRecord->get($this->databaseRecord->getTable()->get(Table::TABLE_ID));
        $this->backup($id);

        $sql = "DELETE FROM"." ".$this->databaseRecord->getTable()->get(Table::TABLE_NAME)."
                WHERE ".$this->databaseRecord->getTable()->get(
                    Table::TABLE_ID)."= :".$this->databaseRecord->getTable()->get(Table::TABLE_ID);
        $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($sql);
        $prepared_statement->bindValue($this->databaseRecord->getTable()->get(Table::TABLE_ID),$id);
        $prepared_statement->execute();
    }

    /**
     * Save the record actual state to the history table.
     *
     * @param int $id
     */
    private function backup(int $id) : void
    {
        // Run select to gather all value stored in the 'actual state' table
        $params = $this->select($id);

        // Insert the previously collected record to the history (history_to = NOW())
        $this->_insert($params, SeparatelyTrackedRecordHandler::INSERT_HIST);
    }

    /**
     * @param array $params
     * @param string $insert_type
     * @return int
     */
    private function _insert(array $params, string $insert_type = "new") : int
    {
        $params_key = array_keys($params);

        switch ($insert_type) {
            case SeparatelyTrackedRecordHandler::INSERT_NEW:
                $table_name = $this->tableDescriptor->get(Table::TABLE_NAME);
                break;
            case SeparatelyTrackedRecordHandler::INSERT_HIST:
                $table_name = $this->tableDescriptor[Table::HISTORY_TABLE_NAME];
                break;
            default:
                throw new \InvalidArgumentException("Invalid insert type! Allowed types: new, history");
        }

        //Prepare insert query
        $insert_query   =   "INSERT INTO"." ".$table_name." (";
        for($i = 0; $i < count($params_key); ++$i) {
            $insert_query.= $params_key[$i];
            if($i < (count($params_key)-1) ) $insert_query.=",";
        }
        // Set record history to actual datetime
        switch ($insert_type) {
            case SeparatelyTrackedRecordHandler::INSERT_NEW:
                $insert_query .= Table::HISTORY_FROM.") VALUES (";
                break;
            case SeparatelyTrackedRecordHandler::INSERT_HIST:
                $insert_query .= Table::HISTORY_TO.") VALUES (";
                break;
            default:
                throw new \InvalidArgumentException("Invalid insert type! Allowed types: new, history");
        }

        for($i = 0; $i < count($params_key); ++$i) {
            $insert_query.= ":".$params_key[$i];
            if($i < (count($params_key)-1) ) $insert_query.=",";
        }
        $insert_query.= "NOW())";

        $prepared_statement = ConnectionManager::getInstance()->getConnection()->prepare($insert_query);
        for($i = 0; $i < count($params); ++$i) {
            $prepared_statement->bindValue($params_key[$i],$params[$params_key[$i]]);
        }

        //Run query
        $prepared_statement->execute();
        $insert_id = ConnectionManager::getInstance()->getConnection()->lastInsertId();
        unset($prepared_statement);

        return intval($insert_id);
    }
}