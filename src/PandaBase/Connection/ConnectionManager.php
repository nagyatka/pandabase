<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 02. 26.
 * Time: 15:33
 */

namespace PandaBase\Connection;


use PandaBase\Exception\ConnectionNotExistsException;
use PandaBase\Record\InstanceRecord;
use PandaBase\Record\InstanceRecordContainer;
use PandaBase\Record\MixedRecordContainer;

/**
 * Class ConnectionManager
 * ConnectionManager manages one or more Connection object. You can get ConnectionManager instance via getInstance method.
 * 
 * @package PandaBase\Connection
 */
class ConnectionManager {
    /**
     * Singleton instance
     *
     * @var ConnectionManager
     */
    private static $connectionManagerInstance = null;

    /**
     * @var Connection[]
     */
    private $connectionInstances;

    /**
     * @var string
     */
    private $defaultConnectionName;

    /**
     * ConnectionManager constructor.
     */
    private function __construct() {
        $this->connectionInstances = [];
    }

    /**
     * Returns with the ConnectionManager instance.
     *
     * @return ConnectionManager
     */
    public static function getInstance() {
        if(ConnectionManager::$connectionManagerInstance === null) {
            ConnectionManager::$connectionManagerInstance = new ConnectionManager();
        }
        return ConnectionManager::$connectionManagerInstance;
    }

    /**
     * Release all connection.
     */
    public static function emptyConnections() {
        ConnectionManager::getInstance()->connectionInstances = [];
        ConnectionManager::getInstance()->defaultConnectionName = "";
    }

    /**
     * Initializes a connection with the parameters and adds it to manager object.
     * @param array $config
     * @throws ConnectionNotExistsException
     */
    public function initializeConnection(array $config) {
        $configuration = ConnectionConfiguration::generateConfiguration($config);
        $numberOfConnection = count($this->connectionInstances);
        $this->connectionInstances[$configuration->getName()] = new Connection($configuration);
        if($numberOfConnection == 0) $this->setDefault($configuration->getName());
    }

    /**
     * Initializes more connections with the parameters and adds them to manager object.
     * @param array
     * @throws ConnectionNotExistsException
     */
    public function initializeConnections($configs) {
        $numberOfConnection = count($this->connectionInstances);
        foreach ($configs as $config) {
            $this->initializeConnection($config);
        }
        if($numberOfConnection == 0) $this->setDefault($configs[0]["name"]);
    }

    public function getConnection($name = null) {
        if($name == null) $name = $this->defaultConnectionName;
        if(!array_key_exists($name,$this->connectionInstances)) {
            throw new ConnectionNotExistsException($name);
        }
        return $this->connectionInstances[$name];
    }

    public function getAllConnection() {
        return $this->connectionInstances;
    }

    public function setDefault($name) {
        if(!array_key_exists($name,$this->connectionInstances)) {
            throw new ConnectionNotExistsException($name);
        }
        $this->defaultConnectionName = $name;
    }

    public function getDefault() {
        return $this->connectionInstances[$this->defaultConnectionName];
    }

    /**
     * @param string $query_string
     * @param array $params
     * @param string $connectionName
     * @throws ConnectionNotExistsException
     * @return MixedRecordContainer
     */
    public function getMixedRecords($query_string,$params=[],$connectionName=null) {
        $query_result = $this->getConnection($connectionName)->fetchAll($query_string,$params);
        return new MixedRecordContainer($query_result == false ? array() : $query_result);
    }

    /**
     * @param string $class_name
     * @param string $query_string
     * @param array $params
     * @param string $connectionName
     * @throws ConnectionNotExistsException
     * @return InstanceRecordContainer
     */
    public function getInstanceRecords($class_name,$query_string,$params=[],$connectionName=null) {
        $query_result = $this->getConnection($connectionName)->fetchAll($query_string,$params);
        $records = array();
        foreach ($query_result as $result) {
            $records[] = new $class_name(0,$result);
        }
        return new InstanceRecordContainer($records);
    }

    /**
     * @param InstanceRecord $instanceRecord
     * @param string $connectionName
     * @throws \Exception
     */
    public function persist(InstanceRecord &$instanceRecord,$connectionName=null) {
        $prevConnectionName = $this->defaultConnectionName;
        if($connectionName != null){
            $this->setDefault($connectionName);
        }

        if(!$instanceRecord instanceof InstanceRecord) {
            throw new \Exception("The given record is not instance of InstanceRecord");
        }

        if($instanceRecord->isNewInstance()) {
            $insertId = $instanceRecord->getRecordHandler()->insert();
            $instanceRecord[$instanceRecord->getTableDescriptor()->get(TABLE_ID)] = $insertId;

        }
        $instanceRecord->getRecordHandler()->edit();

        $this->defaultConnectionName = $prevConnectionName;
    }

    /**
     * @param InstanceRecordContainer $instanceRecordContainer
     */
    public function persistAll(InstanceRecordContainer $instanceRecordContainer) {
        $instanceRecordContainer->foreachRecords(function($record){
            $this->persist($record);
        });
    }

} 