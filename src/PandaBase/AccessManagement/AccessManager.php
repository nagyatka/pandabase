<?php

namespace PandaBase\AccessManagement;
use PandaBase\Connection\ConnectionManager;

/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2016. 08. 14.
 * Time: 11:50
 */
class AccessManager
{
    const TYPE_READ = 4;
    const TYPE_WRITE= 2;

    const OWNER_USER = "owner";
    const OTHER_USER = "other";

    /**
     * @var null|AccessUserInterface
     */
    private $accessUser;

    public function __construct()
    {
        $this->accessUser = null;
    }

    /**
     * @param AccessUserInterface $accessUser
     */
    public function registerAccessUser(AccessUserInterface $accessUser) {
        $this->accessUser = $accessUser;
    }

    /**
     * @return null|AccessUserInterface
     */
    public function getAccessUser() {
        return $this->accessUser;
    }

    /**
     *
     * Figyelem! Ha nincs beállított user, akkor a visszatérési érték hamis!!
     *
     * @param AccessibleObject $object
     * @param int $access_type
     * @return bool
     */
    private function checkAccess(AccessibleObject $object,$access_type) {
        //Ha nincs user, akkor semmiképpen nem biztosítunk hozzáférést
        if($this->accessUser == null) {
            return false;
        }
        //Ha root, akkor mindig kap mindenre jogot
        if ($this->accessUser->isRoot()) {
            return true;
        }
        if($this->accessUser->getUserId() === $object->getOwnerId()) {
            return $object->getAccessRules(AccessManager::OWNER_USER)[$access_type];
        } else {
            return $object->getAccessRules(AccessManager::OTHER_USER)[$access_type];
        }
    }

    /**
     * @param AccessibleObject $object
     */
    public function checkReadAccess(AccessibleObject $object) {
        $this->checkAccess($object,AccessManager::TYPE_READ);
    }

    /**
     * @param AccessibleObject $object
     */
    public function checkWriteAccess(AccessibleObject $object) {
        $this->checkAccess($object,AccessManager::TYPE_WRITE);
    }
}