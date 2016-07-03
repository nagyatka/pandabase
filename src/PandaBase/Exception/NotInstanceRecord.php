<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2016. 07. 03.
 * Time: 13:45
 */

namespace PandaBase\Exception;


class NotInstanceRecord extends \Exception
{
    /**
     * NotInstanceRecord constructor.
     * @param string $className
     */
    public function __construct($className)
    {
        parent::__construct($className." is not extended from InstanceRecord", 0, null);
    }
}