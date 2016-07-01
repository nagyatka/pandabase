<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 17.
 * Time: 12:06
 */

namespace PandaBase\Record;


class MixedRecordContainer extends DatabaseRecordContainer {


    function __construct(array $records)
    {
        $databaseRecords = [];
        foreach ($records as $record) {
            $databaseRecords[] = new MixedRecord($record);
        }
        parent::__construct($databaseRecords);
    }
}