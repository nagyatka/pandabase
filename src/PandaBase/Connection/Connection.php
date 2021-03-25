<?php

namespace PandaBase\Connection;


use InvalidArgumentException;
use PandaBase\Connection\Scheme\Table;
use PDO;

/**
 * Class Connection
 * PDO wrapper class.
 * @package PandaBase\Connection
 */
class Connection {

    const TABLES = "tables";
    const ATTRIBUTES = "attributes";

    /**
     * PHP PDO object.
     * @var PDO
     */
    private $database;

    /**
     * @var ConnectionConfiguration
     */
    private $connectionConfiguration;

    /**
     * Connection constructor.
     * @param ConnectionConfiguration $configuration
     */
    public function __construct(ConnectionConfiguration $configuration) {
        $this->connectionConfiguration = $configuration;
        $pdoString = Connection::pdoDsnFactory($configuration);
        $this->database = new PDO($pdoString, $this->connectionConfiguration->getUser(), $this->connectionConfiguration->getPassword());

        $pdoAttributes = $configuration->getPdoAttributes();
        if($pdoAttributes) {
            foreach ($pdoAttributes as $attributeName => $attributeValue) {
                $this->database->setAttribute($attributeName,$attributeValue);
            }
        }

    }

    /**
     * @param ConnectionConfiguration $configuration
     * @return string
     */
    private static function pdoDsnFactory(ConnectionConfiguration $configuration) {
        switch ($configuration->getDriver()) {
            case "mysql":
                return 'mysql:host='.$configuration->getHost().";port=".$configuration->getPort().";dbname=".$configuration->getDbname();
            case "mssql":
                return 'sqlsrv:Server='.$configuration->getHost().';Database='.$configuration->getDbname();
            default:
                throw new \PDOException("Unknown PDO driver!");
        }
    }

    /**
     * Fetch only one record from database.
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function fetchAssoc($sql,$params = []) {
        $stmt = $this->database->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key,$value);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all result from database.
     * @param $sql
     * @param array $params
     * @return array
     */
    public function fetchAll($sql,$params = []) {
        $stmt = $this->database->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key,$value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * PDO prepare.
     * @param $sql
     * @return \PDOStatement
     */
    public function prepare($sql) {
        return $this->database->prepare($sql);
    }

    /**
     * PDO beginTransaction
     * @return bool
     */
    public function beginTransaction() {
        return $this->database->beginTransaction();
    }

    /**
     * PDO rollback.
     * @return bool
     */
    public function rollBack() {
        return $this->database->rollBack();
    }

    /**
     * PDO commit.
     * @return bool
     */
    public function commit() {
        return $this->database->commit();
    }

    /**
     * @return string
     */
    public function lastInsertId(){
        return $this->database->lastInsertId();
    }

    /**
     * Returns with PDO.
     * @return PDO
     */
    public function getDatabase() {
        return $this->database;
    }

    /**
     * Get actual configuration.
     * @return ConnectionConfiguration
     */
    public function getConnectionConfiguration()
    {
        return $this->connectionConfiguration;
    }

    /**
     * Release connection.
     */
    public function release() {
        $this->database = null;
    }

    /**
     * Set names to utf8.
     */
    public function setNamesUTF8() {
        $this->database->query("SET NAMES utf8mb4");
    }

    /**
     * Creates all the tables which were defined in the scheme definition of the ConnectionConfiguration
     * @param bool $forced If it is true, the tables will be created even though some of them are exist already
     */
    public function createTables($forced = false) {
        $tables = $this->connectionConfiguration->getTables();

        foreach ($tables as $table) {
            $sql_parts = [];
            $sql_parts[] = "CREATE TABLE";


            // Table name
            $table_name = $table->get(Table::TABLE_NAME);
            if ($table_name == null) {
                throw new InvalidArgumentException('One of the table name does not exist. Please 
                check the scheme!');
            }
            if (!$forced) $sql_parts[] = "IF NOT EXISTS";
            $sql_parts[] = "$table_name (";


            // Table fields
            $fields = $table_name->get(Table::FIELDS);
            if ($fields == null) {
                throw new InvalidArgumentException("Fields for table $table_name is not defined");
            }
            foreach ($fields as $field_name => $field_definition) {
                $sql_parts[] = "`$field_name` $field_definition,";
            }


            // Primary key
            if($table->get(Table::TABLE_SEQ_ID, null) != null) {
                $primary_key = $table->get(Table::PRIMARY_KEY, [$table->get(Table::TABLE_SEQ_ID)]);
            }
            else {
                $primary_key = $table->get(Table::PRIMARY_KEY, [$table->get(Table::TABLE_ID)]);
            }
            if(!is_array($primary_key)) {
                $primary_key = [$primary_key];
            }
            $sql_parts[] = "PRIMARY KEY (".implode(',', $primary_key).") ,";


            // Indexes
            $indices =  $table->get(Table::INDEX, []);
            $sql_parts[] = "INDEX (".implode(',', $indices).") ,";
            $sql_parts[] = ")";


            // Engine
            $engine = $table->get(Table::ENGINE, 'InnoDB');
            $sql_parts[] = "ENGINE = $engine;";

            $this->database->exec(implode(' ', $sql_parts));

        }

    }

    /**
     * @param $new_scheme
     * @throws \Exception
     */
    public function updateTables($new_scheme) {
        throw new \Exception('Not yet implemented');
    }
}
