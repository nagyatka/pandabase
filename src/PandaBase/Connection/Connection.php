<?php

namespace PandaBase\Connection;


use PDO;

/**
 * Class Connection
 * @package PandaBase\Connection
 */
class Connection {

    /**
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
        $pdoString = 'mysql:host='.$this->connectionConfiguration->getHost().";port=3306;dbname=".$this->connectionConfiguration->getDbname();
        $this->database = new PDO($pdoString, $this->connectionConfiguration->getUser(), $this->connectionConfiguration->getPassword());
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


}