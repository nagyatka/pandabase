<?php

namespace PandaBase\Connection;


use PDO;

/**
 * Class Connection
 * PDO wrapper class.
 * @package PandaBase\Connection
 */
class Connection {

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
                return 'mysql:host='.$configuration->getHost().";port=3306;dbname=".$configuration->getDbname();
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
        $this->database->query("SET NAMES utf8");
    }


}