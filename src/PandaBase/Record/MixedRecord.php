<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 14.
 * Time: 22:21
 */

namespace PandaBase\Record;


class MixedRecord extends DatabaseRecord {

    /**
     * @param array $values
     */
    function __construct(array $values)
    {
        parent::__construct(new TableDescriptor([]),$values);
    }


    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * @param TableDescriptor $tableDescriptor
     * @return RecordHandler
     */
    public function getRecordHandler(TableDescriptor $tableDescriptor = null)
    {
        return new MixedRecordHandler();
    }


}