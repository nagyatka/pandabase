<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 25.
 * Time: 16:08
 */

namespace PandaBase\Record;


class ModerableSimpleRecordHandler extends SimpleRecordHandler {
    public function insert()
    {
        $this->databaseRecord->set("under_moderation",1);
        return parent::insert();
    }
}