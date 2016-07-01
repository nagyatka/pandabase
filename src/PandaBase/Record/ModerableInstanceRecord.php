<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 16.
 * Time: 17:41
 */

namespace PandaBase\Record;


abstract class ModerableInstanceRecord extends InstanceRecord {

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        if(array_key_exists($key,$this->getModerationFields())) $this->setToModerated();
        $this->values[$key] = $value;
    }

    public function isModerated() {
        return $this["under_moderation"] == 1;
    }

    public function setToModerated() {
        $this["under_moderation"] = 0;
    }

    public function setToUnderModeration() {
        $this["under_moderation"] = 1;
    }

    /**
     * @return array
     */
    public abstract function getModerationFields();
}