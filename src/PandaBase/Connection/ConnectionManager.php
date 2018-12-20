<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 02. 26.
 * Time: 15:33
 */

namespace PandaBase\Connection;


use PandaBase\AccessManagement\AccessibleObject;
use PandaBase\AccessManagement\AccessManager;
use PandaBase\AccessManagement\AuthorizedUserInterface;
use PandaBase\Connection\Scheme\Table;
use PandaBase\Exception\AccessDeniedException;
use PandaBase\Exception\ConnectionNotExistsException;
use PandaBase\Exception\NotInstanceRecordException;
use PandaBase\Record\InstanceRecord;

/**
 * Class ConnectionManager
 * ConnectionManager manages one or more Connection object. You can get the singleton instance via getInstance method
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
    public function releaseConnection(string $name = null) {
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
     * It releases all connection.
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
        $numberOfActiveConnection = count($this->connectionInstances);
        $this->connectionInstances[$configuration->getName()] = new Connection($configuration);
        if($numberOfActiveConnection == 0) $this->setDefault($configuration->getName());
    }

    /**
     * Initializes connections with the parameters and adds them to manager object.
     * The first connection will be the default connection.
     *
     * @param array $configs Configuration settings
     * @throws ConnectionNotExistsException
     */
    public function initializeConnections(array $configs) {
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
    public function getConnection(string $name = null) {
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
    public function exist(string $name) {
        return isset($this->connectionInstances[$name]);
    }

    /**
     * Set the default connection.
     *
     * @param string $name Name of the connection
     * @throws ConnectionNotExistsException
     */
    public function setDefault(string $name) {
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
     * Returns with array of records (values of a record have been stored in associative array) based on sql string and
     * sql params. If you use the method without connectionName it uses the default connection, by the way it will use
     * the selected connection if it exists.
     *
     * @param string $query_string SQL string
     * @param array $params Parameters for query
     * @param string $connectionName Name of the connection
     * @throws ConnectionNotExistsException
     * @return array
     */
    public static function fetchAll(string $query_string, array $params = [], string $connectionName = null) {
        $connectionManager = ConnectionManager::getInstance();
        $query_result = $connectionManager->getConnection($connectionName)->fetchAll($query_string,$params);
        return ($query_result == false ? array() : $query_result);
    }

    /**
     * Returns with an associative array of requested result based on sql string and sql params. If you use the method
     * without connectionName it uses the default connection, by the way it will use the selected connection if it
     * exists.
     *
     * @param string $query_string
     * @param array $params
     * @param string|null $connectionName
     * @return array|mixed
     */
    public static function fetchAssoc(string $query_string, array $params = [], string $connectionName = null) {
        $connectionManager = ConnectionManager::getInstance();
        $query_result = $connectionManager->getConnection($connectionName)->fetchAssoc($query_string,$params);
        return ($query_result == false ? array() : $query_result);
    }

    /**
     * Returns with array of InstanceRecord based on sql string and sql params. If you use the method without connectionName
     * it will use the default connection, by the way it will use the selected connection if it exists.
     *
     * @param string $class_name Name of the class.
     * @param string $query_string SQL string
     * @param array $params Paramters for query
     * @param string $connectionName Name of the connection
     * @throws ConnectionNotExistsException
     * @return array
     */
    public static function getInstanceRecords(string $class_name, string $query_string, array $params = [], string $connectionName = null) {
        $connectionManager = ConnectionManager::getInstance();
        $query_result = $connectionManager->getConnection($connectionName)->fetchAll($query_string,$params);
        $records = array();
        foreach ($query_result as $result) {
            $records[] = new $class_name($result);
        }
        return $records;
    }

    /**
     * Save the InstanceRecord in the database. If you use the method without connectionName
     * it will use the default connection, by the way it will use the selected connection if it exists.
     *
     * @param InstanceRecord $instanceRecord 
     * @param string $connectionName
     * @throws \Exception
     */
    public static function persist(InstanceRecord &$instanceRecord, string $connectionName = null) {
        $connectionManager = ConnectionManager::getInstance();
        $prevConnectionName = $connectionManager->defaultConnectionName;
        if($connectionName != null){
            $connectionManager->setDefault($connectionName);
        }

        if(!$instanceRecord instanceof InstanceRecord) {
            throw new NotInstanceRecordException(get_class($instanceRecord));
        }

        // Check permissions
        if(in_array(AccessibleObject::class,class_uses($instanceRecord))) {
            /** @var AccessibleObject $object */
            $object = $instanceRecord;
            if(!ConnectionManager::getInstance()->getAccessManager()->checkWriteAccess($object)) {
                throw new AccessDeniedException;
            }
        }

        if($instanceRecord->isNewInstance()) {
            $insertId = $instanceRecord->getRecordHandler()->insert();
            $instanceRecord[$instanceRecord->getTable()->get(Table::TABLE_ID)] = $insertId;

        } else {
            $instanceRecord->getRecordHandler()->edit();
        }

        $connectionManager->defaultConnectionName = $prevConnectionName;
    }

    /**
     * Save all InstanceRecord in the database. If you use the method without connectionName
     * it will use the default connection, by the way it will use the selected connection if it exists.
     *
     * @param array $instanceRecords
     * @param string|null $connectionName
     */
    public static function persistAll(array $instanceRecords, string $connectionName = null) {
        foreach ($instanceRecords as $instanceRecord) {
            ConnectionManager::persist($instanceRecord,$connectionName);
        }
    }

    /**
     * @param AuthorizedUserInterface $accessUser
     */
    public function registerAuthorizedUser(AuthorizedUserInterface $accessUser) {
        $this->accessManager->registerUser($accessUser);
    }

    /**
     * @return AccessManager
     */
    public function getAccessManager() {
        return $this->accessManager;
    }

    /**
     * @param string $class_name
     * @return Table
     */
    public static function getTable(string $class_name) {
        return ConnectionManager::getInstance()
            ->getConnection() // Get actual connection
            ->getConnectionConfiguration()
            ->getTable($class_name);
    }
} 