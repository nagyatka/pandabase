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
     * @return int
     */
    abstract public function getOwnerGroupId();

    /**
     * @param string $user_type
     * @return string
     */
    public function getAccessRules($user_type){
        return $this->rules[$user_type];
    }

    /**
     * [owner]:[group]:[other]:[anon]
     *
     * pl.:
     * Tulajdonos írhat és olvashat, többi user olvashat --> rw:r:r:
     * @param string $rules
     */
    public function setAccessRules($rules) {

        $parts = explode(":",$rules);
        //Ha nincs elég rész, akkor hibát dobunk
        if(count($parts) != 4) {
            throw new \InvalidArgumentException("You must provide [owner]:[group]:[other]:[anon] access rules.");
        }

        $rules[AccessManager::OWNER_USER][AccessManager::TYPE_READ] = strpos($parts[0],"r") ? true : false;
        $rules[AccessManager::OWNER_USER][AccessManager::TYPE_WRITE] = strpos($parts[0],"w") ? true : false;

        $rules[AccessManager::GROUP_USER][AccessManager::TYPE_READ] = strpos($parts[1],"r") ? true : false;
        $rules[AccessManager::GROUP_USER][AccessManager::TYPE_WRITE] = strpos($parts[1],"w") ? true : false;

        $rules[AccessManager::OTHER_USER][AccessManager::TYPE_READ] = strpos($parts[2],"r") ? true : false;
        $rules[AccessManager::OTHER_USER][AccessManager::TYPE_WRITE] = strpos($parts[2],"w") ? true : false;

        $rules[AccessManager::ANON_USER][AccessManager::TYPE_READ] = strpos($parts[3],"r") ? true : false;
        $rules[AccessManager::ANON_USER][AccessManager::TYPE_WRITE] = strpos($parts[3],"w") ? true : false;
    }
}