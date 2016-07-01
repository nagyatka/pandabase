<?php

namespace PandaBase\Exception;


use Exception;

/**
 * Class ConnectionNotExistsException
 * @package PandaBase\Database\Connection
 */
class ConnectionNotExistsException extends \Exception{

    /**
     * ConnectionNotExistsException constructor.
     * @param string $connectionName
     */
    public function __construct($connectionName)
    {
        parent::__construct("Connection (named as ".$connectionName.") does not exists!", 0, null);
    }
} 