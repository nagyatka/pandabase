<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 14.
 * Time: 22:21
 */

namespace PandaBase\Record;


use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\TableDescriptor;
use PandaBase\Exception\DatabaseManagerNotExists;

abstract class RecordHandler {

    /**
     * @var ConnectionManager
     */
    private $connectionManager;

    /**
     * @var InstanceRecord
     */
    protected $databaseRecord;

    /**
     * @var TableDescriptor
     */
    protected $tableDescriptor;

    /**
     * @param TableDescriptor $tableDescriptor
     * @throws DatabaseManagerNotExists
     */
    function __construct(TableDescriptor $tableDescriptor) {
        $this->connectionManager = ConnectionManager::getInstance();
        $this->tableDescriptor = $tableDescriptor;
        $this->databaseRecord = null;
    }

    function setManagedRecord(DatabaseRecord $databaseRecord) {
        $this->databaseRecord = $databaseRecord;
        $this->tableDescriptor = $this->databaseRecord->getTableDescriptor();
    }

    /**
     * @return int
     */
    abstract public function insert();

    /**
     * @param int $id
     * @return array
     */
    abstract public function select($id);

    /**
     * @return mixed
     */
    abstract public function edit();

    /**
     * @return mixed
     */
    abstract public function remove();

} 