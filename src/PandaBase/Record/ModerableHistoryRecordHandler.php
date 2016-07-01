<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 25.
 * Time: 16:13
 */

namespace PandaBase\Record;


class ModerableHistoryRecordHandler extends HistoryAbleRecordHandler {

    public function insert()
    {
        $this->databaseRecord->set("under_moderation",1);
        return parent::insert();
    }
} 