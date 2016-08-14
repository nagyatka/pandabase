<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2016. 08. 14.
 * Time: 12:03
 */

namespace PandaBase\AccessManagement;


trait AccessibleObject
{

    private $rules;

    /**
     * @return int
     */
    abstract public function getOwnerId();

    /**
     * @param string $user_type
     * @return string
     */
    public function getAccessRules($user_type){
        return $this->rules[$user_type];
    }

    /**
     *
     * rw:r
     * @param string $rules
     */
    public function setAccessRules($rules) {
        $parts = explode(":",$rules);
        $rules[AccessManager::OWNER_USER][AccessManager::TYPE_READ] = strpos($parts[0],"r") ? true : false;
        $rules[AccessManager::OWNER_USER][AccessManager::TYPE_WRITE] = strpos($parts[0],"w") ? true : false;

        if(isset($parts[1])) {
            $rules[AccessManager::OTHER_USER][AccessManager::TYPE_READ] = strpos($parts[1],"r") ? true : false;
            $rules[AccessManager::OTHER_USER][AccessManager::TYPE_WRITE] = strpos($parts[1],"w") ? true : false;
        }
    }
}