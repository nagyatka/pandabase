<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 14.
 * Time: 22:21
 */

namespace PandaBase\Record;


use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\Scheme\Table;
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
     * @var Table
     */
    protected $tableDescriptor;

    /**
     * @param Table $tableDescriptor
     * @throws DatabaseManagerNotExists
     */
    function __construct(Table $tableDescriptor) {
        $this->connectionManager = ConnectionManager::getInstance();
        $this->tableDescriptor = $tableDescriptor;
        $this->databaseRecord = null;
    }

    /**
     * @param InstanceRecord $instanceRecord
     */
    function setManagedRecord(InstanceRecord $instanceRecord) {
        $this->databaseRecord = $instanceRecord;
        $this->tableDescriptor = $this->databaseRecord->getTable();
    }

    /**
     * @return int
     */
    abstract public function insert(): int;

    /**
     * @param int $id
     * @return array
     */
    abstract public function select(int $id);

    /**
     * @param string $column_name
     * @param mixed $value
     * @return array
     */
    abstract public function list(string $column_name, mixed $value): array;

    /**
     * @return mixed
     */
    abstract public function edit(): mixed;

    /**
     * @return void
     */
    abstract public function remove();

} 