<?php

namespace PandaBase\Connection;

use PandaBase\Connection\Scheme\Table;
use PandaBase\Exception\TableNotExists;

/**
 * Class ConnectionConfiguration
 *
 * ConnectionConfiguration object contains PDO settings.
 *
 * @package PandaBase\Connection
 */
class ConnectionConfiguration {

    /**
     * Name of the connection.
     * @var string
     */
    private $name;

    /**
     * Host of the database.
     * @var string
     */
    private $host;

    /**
     * Host of the database.
     * @var string
     */
    private $port;

    /**
     * Name of the database.
     * @var string
     */
    private $dbname;

    /**
     * Username
     * @var string
     */
    private $user;

    /**
     * Password
     * @var string
     */
    private $password;

    /**
     * PDO driver
     * @var string
     */
    private $driver;

    /**
     * PDO attributes
     * @var array
     */
    private $pdoAttributes;

    /**
     * List of supported drivers.
     * @var array
     */
    private $supportedDrivers = ["mysql","mssql"];

    /**
     * Set of used table descriptors.
     *
     * @var Table[]
     */
    private $tables;

    /**
     * Constructor.
     *
     * Warning: At this moment we support only mysql driver
     *
     * @param string $dbname Name of the database
     * @param $driver string Database type. Supported drivers: mysql
     * @param $host string Host
     * @param string $port Port number.
     * @param $name string Name of the connection.
     * @param $password string Database password
     * @param $user string Database username
     * @param array $pdoAttributes
     * @param Table[] $tables
     * @throws \Exception
     */
    private function __construct(string $dbname, string $driver, $host, $port, $name, $password, $user, $pdoAttributes, $tables)
    {
        if(!in_array($driver,$this->supportedDrivers))
            throw new \Exception("Unsupported PDO driver. List of supported drivers:".implode(",",$this->supportedDrivers));

        $this->dbname = $dbname;
        $this->driver = $driver;
        if($host=="localhost") $host = "127.0.0.1"; // To ensure that php does not use unix socket instead of tcp.
        $this->host = $host;
        $this->port = $port;
        $this->name = $name;
        $this->password = $password;
        $this->user = $user;
        $this->pdoAttributes = $pdoAttributes;
        $this->tables = $tables;
    }

    /**
     * Generate a ConnectionConfiguration object from an array.
     *
     * @param array $configArray
     * @return ConnectionConfiguration
     * @throws \Exception
     */
    public static function generateConfiguration(array $configArray) {

        if(!isset($configArray["port"])) {
            $configArray["port"] = 3306;
        }

        return new ConnectionConfiguration(
            $configArray["dbname"],
            $configArray["driver"],
            $configArray["host"],
            $configArray["port"],
            $configArray["name"],
            $configArray["password"],
            $configArray["user"],
            $configArray[Connection::ATTRIBUTES] ?? [],
            $configArray[Connection::TABLES] ?? []
        );
    }

    /**
     * Returns with dbname.
     * @return string
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * Returns with driver.
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns with host.
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * Returns with connection's name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns with username.
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getPdoAttributes()
    {
        return $this->pdoAttributes;
    }

    /**
     * @return array
     */
    public function getSupportedDrivers()
    {
        return $this->supportedDrivers;
    }

    /**
     * @param $class_name
     * @return Table
     * @throws TableNotExists
     */
    public function getTable($class_name) {
        if(!array_key_exists($class_name,$this->tables)) {
            throw new TableNotExists($class_name. " does not exist in Connection configuration");
        }
        return $this->tables[$class_name];
    }

    public function getTables() {
        return $this->tables;
    }
} 