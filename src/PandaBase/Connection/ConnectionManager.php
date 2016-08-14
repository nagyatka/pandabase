<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 02. 26.
 * Time: 15:33
 */

namespace PandaBase\Connection;


use PandaBase\AccessManagement\AccessManager;
use PandaBase\AccessManagement\AccessUserInterface;
use PandaBase\Exception\ConnectionNotExistsException;
use PandaBase\Exception\NotInstanceRecord;
use PandaBase\Record\InstanceRecord;
use PandaBase\Record\InstanceRecordContainer;
use PandaBase\Record\MixedRecordContainer;

/**
 * Class ConnectionManager
 * ConnectionManager manages one or more Connection object. You can get ConnectionManager instance via getInstance method
 * globally.
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
     * Connection instances
     *
     * @var Connection[]
     */
    private $connectionInstances;

    /**
     * Name of the default connection
     *
     * @var string
     */
    private $defaultConnectionName;

    /**
     * @var AccessManager
     */
    private $accessManager;

    /**
     * ConnectionManager constructor.
     */
    private function __construct() {
        $this->connectionInstances = [];
        $this->accessManager = new AccessManager();
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
     * Release the connection based on parameter.
     * If you use it without parameter, function tries to release the default connection.
     *
     * @param string|null $name Name of the releaseable connection.
     * @throws ConnectionNotExistsException
     */
    public function releaseConnection($name = null) {
        $originalName = $name;
        if($name == null) {
            $name = $this->defaultConnectionName;
        }

        if(!$this->exist($name)) {
            throw new ConnectionNotExistsException($originalName == null ? "default" : $name);
        }

        $this->connectionInstances[$name]->release();
        unset($this->connectionInstances[$name]);
    }

    /**
     * Release all connection.
     *
     */
    public function releaseConnections() {
        foreach ($this->getAllConnection() as $connection) {
            $connection->release();
        }
        ConnectionManager::getInstance()->connectionInstances = [];
        ConnectionManager::getInstance()->defaultConnectionName = "";
    }

    /**
     * Initializes a connection with the parameters and adds it to manager object.
     *
     * @param array $config Configuration setting
     * @throws ConnectionNotExistsException
     */
    public function initializeConnection(array $config) {
        $configuration = ConnectionConfiguration::generateConfiguration($config);
        $numberOfConnection = count($this->connectionInstances);
        $this->connectionInstances[$configuration->getName()] = new Connection($configuration);
        if($numberOfConnection == 0) $this->setDefault($configuration->getName());
    }

    /**
     * Initializes connections with the parameters and adds them to manager object.
     * The first connection will be the default connection.
     *
     * @param array $configs Configuration settings
     * @throws ConnectionNotExistsException
     */
    public function initializeConnections($configs) {
        $numberOfConnection = count($this->connectionInstances);
        foreach ($configs as $config) {
            $this->initializeConnection($config);
        }
        if($numberOfConnection == 0) $this->setDefault($configs[0]["name"]);
    }

    /**
     * Returns with connection named as in parameter.
     *
     * @param string|null $name Name of the connection
     * @return mixed|Connection
     * @throws ConnectionNotExistsException
     */
    public function getConnection($name = null) {
        if($name == null) $name = $this->defaultConnectionName;
        if(!$this->exist($name)) {
            throw new ConnectionNotExistsException($name);
        }
        return $this->connectionInstances[$name];
    }

    /**
     * Returns with list of all connection.
     *
     * @return array|Connection[] List of connection.
     */
    public function getAllConnection() {
        return $this->connectionInstances;
    }


    /**
     * Check connection exist or not.
     *
     * @param string $name Name of the connection
     * @return bool True if it exists
     */
    public function exist($name) {
        return isset($this->connectionInstances[$name]);
    }

    /**
     * Set the default connection.
     *
     * @param string $name Name of the connection
     * @throws ConnectionNotExistsException
     */
    public function setDefault($name) {
        if(!$this->exist($name)) {
            throw new ConnectionNotExistsException($name);
        }
        $this->defaultConnectionName = $name;
    }

    /**
     * Returns with default connection.
     *
     * @return mixed|Connection
     * @throws ConnectionNotExistsException
     */
    public function getDefault() {
        if(!$this->exist($this->defaultConnectionName))
            throw new ConnectionNotExistsException("default");
        return $this->connectionInstances[$this->defaultConnectionName];
    }

    /**
     * Returns with MixedRecordContainer based on sql string and sql params. If you use the method without connectionName
     * it will use the default connection, by the way it will use the selected connection if it exists.
     *
     * @param string $query_string SQL string
     * @param array $params Parameters for query
     * @param string $connectionName Name of the connection
     * @throws ConnectionNotExistsException
     * @return MixedRecordContainer
     */
    public function getMixedRecords($query_string,$params=[],$connectionName=null) {
        $query_result = $this->getConnection($connectionName)->fetchAll($query_string,$params);
        return new MixedRecordContainer($query_result == false ? array() : $query_result);
    }

    /**
     * Returns with InstanceRecordContainer based on sql string and sql params. If you use the method without connectionName
     * it will use the default connection, by the way it will use the selected connection if it exists.
     *
     * @param string $class_name Name of the class.
     * @param string $query_string SQL string
     * @param array $params Paramters for query
     * @param string $connectionName Name of the connection
     * @throws ConnectionNotExistsException
     * @return InstanceRecordContainer
     */
    public function getInstanceRecords($class_name,$query_string,$params=[],$connectionName=null) {
        $query_result = $this->getConnection($connectionName)->fetchAll($query_string,$params);
        $records = array();
        foreach ($query_result as $result) {
            $records[] = new $class_name(0,$result);
        }
        return new InstanceRecordContainer($class_name,$records);
    }

    /**
     * Save the InstanceRecord in the database. If you use the method without connectionName
     * it will use the default connection, by the way it will use the selected connection if it exists.
     *
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
            throw new NotInstanceRecord(get_class($instanceRecord));
        }

        if($instanceRecord->isNewInstance()) {
            $insertId = $instanceRecord->getRecordHandler()->insert();
            $instanceRecord[$instanceRecord->getTableDescriptor()->get(TABLE_ID)] = $insertId;

        }
        $instanceRecord->getRecordHandler()->edit();

        $this->defaultConnectionName = $prevConnectionName;
    }

    /**
     * Save all InstanceRecord in the database. If you use the method without connectionName
     * it will use the default connection, by the way it will use the selected connection if it exists.
     *
     * @param InstanceRecordContainer $instanceRecordContainer
     * @param string|null $connectionName
     */
    public function persistAll(InstanceRecordContainer $instanceRecordContainer,$connectionName=null) {
        $instanceRecordContainer->foreachRecords(function($record) use($connectionName) {
            $this->persist($record,$connectionName);
        });
    }

    /**
     * @param AccessUserInterface $accessUser
     */
    public function registerAccessUser(AccessUserInterface $accessUser) {
        $this->accessManager->registerAccessUser($accessUser);
    }

    /**
     * @return AccessManager
     */
    public function getAccessManager() {
        return $this->accessManager;
    }
} 