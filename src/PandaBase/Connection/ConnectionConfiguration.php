<?php

namespace PandaBase\Connection;

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
     * Constructor.
     *
     * Warning: At this moment we support only mysql driver
     *
     * @param $dbname string Name of the database
     * @param $driver string Database type. Supported drivers: mysql
     * @param $host string Host
     * @param $name string Name of the connection.
     * @param $password string Database password
     * @param $user string Database username
     * @param array $pdoAttributes
     * @throws \Exception
     */
    function __construct($dbname, $driver, $host, $name, $password, $user, $pdoAttributes = null)
    {
        if(!in_array($driver,$this->supportedDrivers))
            throw new \Exception("Unsupported PDO driver. List of supported drivers:".implode(",",$this->supportedDrivers));

        $this->dbname = $dbname;
        $this->driver = $driver;
        if($host=="localhost") $host = "127.0.0.1";
        $this->host = $host;
        $this->name = $name;
        $this->password = $password;
        $this->user = $user;
        $this->pdoAttributes = $pdoAttributes;
    }

    /**
     * Generate a ConnectionConfiguration object from an array.
     * @param array $configArray
     * @return ConnectionConfiguration
     */
    public static function generateConfiguration(array $configArray) {
        return new ConnectionConfiguration(
            $configArray["dbname"],
            $configArray["driver"],
            $configArray["host"],
            $configArray["name"],
            $configArray["password"],
            $configArray["user"],
            $configArray["attributes"]
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


} 