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
     * @param string $className
     */
    public function __construct($className)
    {
        parent::__construct("Connection (named as ".$className.") does not exists!", 0, null);
    }
} 