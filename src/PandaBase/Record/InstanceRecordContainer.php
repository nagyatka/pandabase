<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 17.
 * Time: 12:06
 */

namespace PandaBase\Record;


class InstanceRecordContainer extends DatabaseRecordContainer {

    private $className;

    /**
     * InstanceRecordContainer constructor.
     * @param string $className
     * @param DatabaseRecord[] $records
     */
    public function __construct($className,$records)
    {
        $this->className = $className;
        parent::__construct($records);
    }
} 