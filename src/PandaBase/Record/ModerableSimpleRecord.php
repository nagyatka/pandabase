<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 16.
 * Time: 18:24
 */

namespace PandaBase\Record;


abstract class ModerableSimpleRecord extends ModerableInstanceRecord {

    /**
     * @param TableDescriptor $tableDescriptor
     * @return RecordHandler
     */
    public function getRecordHandler(TableDescriptor $tableDescriptor = null)
    {
        if($tableDescriptor == null) {
            $simpleHandler = new ModerableSimpleRecordHandler($this->getTableDescriptor());
            $simpleHandler->setManagedRecord($this);
            return $simpleHandler;
        } else {
            return new ModerableSimpleRecordHandler($tableDescriptor);
        }
    }
}