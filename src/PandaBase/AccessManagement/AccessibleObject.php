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

    /**
     * @var array
     */
    private $rules;

    /**
     * @return int
     */
    abstract public function getOwnerId(): int;

    /**
     * @return int
     */
    abstract public function getOwnerGroupId(): int;

    /**
     * Returns with the permission of the user type. The allowable user types are defined in AccessManager class as
     * consts.
     * @param string $user_type
     * @return array
     */
    public function getAccessRules($user_type): array {
        return $this->rules[$user_type];
    }

    /**
     * Set access rules for an object in the following way: [owner]:[group]:[other]:[anon]
     * Example: rw:r:r: --> The owner has read and write permission, others have only read access
     *
     * @param string $rules Rules separated by ':'
     * @return void
     */
    public function setAccessRules($rules) {

        $parts = explode(":",$rules);
        //Ha nincs elég rész, akkor hibát dobunk
        if(count($parts) != 4) {
            throw new \InvalidArgumentException("You must provide [owner]:[group]:[other]:[anon] access rules.");
        }

        $this->rules[AccessManager::OWNER_USER][AccessManager::TYPE_READ] = strpos($parts[0],"r") ? true : false;
        $this->rules[AccessManager::OWNER_USER][AccessManager::TYPE_WRITE] = strpos($parts[0],"w") ? true : false;

        $this->rules[AccessManager::GROUP_USER][AccessManager::TYPE_READ] = strpos($parts[1],"r") ? true : false;
        $this->rules[AccessManager::GROUP_USER][AccessManager::TYPE_WRITE] = strpos($parts[1],"w") ? true : false;

        $this->rules[AccessManager::OTHER_USER][AccessManager::TYPE_READ] = strpos($parts[2],"r") ? true : false;
        $this->rules[AccessManager::OTHER_USER][AccessManager::TYPE_WRITE] = strpos($parts[2],"w") ? true : false;

        $this->rules[AccessManager::ANON_USER][AccessManager::TYPE_READ] = strpos($parts[3],"r") ? true : false;
        $this->rules[AccessManager::ANON_USER][AccessManager::TYPE_WRITE] = strpos($parts[3],"w") ? true : false;
    }
}