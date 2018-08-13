<?php

namespace PandaBase\AccessManagement;


class AccessManager
{
    const TYPE_READ = 4;
    const TYPE_WRITE= 2;

    const OWNER_USER = "owner";
    const GROUP_USER = "group";
    const OTHER_USER = "other";
    const ANON_USER  = "anon";

    /**
     * @var null|AuthenticatedUserInterface
     */
    private $accessUser;

    /**
     * AccessManager constructor.
     */
    public function __construct()
    {
        $this->accessUser = null;
    }

    /**
     * @param AuthenticatedUserInterface $accessUser
     * @return void
     */
    public function registerUser(AuthenticatedUserInterface $accessUser) {
        $this->accessUser = $accessUser;
    }

    /**
     * @return null|AuthenticatedUserInterface
     */
    public function getUser() {
        return $this->accessUser;
    }

    /**
     *
     * WARNING! If the registered AuthenticatedUser equals with null in AccessManager the return value is always false.
     *
     * @param AccessibleObject $object
     * @param int $access_type
     * @return bool
     */
    private function checkAccess(AccessibleObject $object,$access_type) {

        //Ha nincs beállított user, akkor anonym hozzáférés
        if($this->getUser() == null) {
            return $object->getAccessRules(AccessManager::ANON_USER)[$access_type];
        }

        //Ha root, akkor mindig kap mindenre jogot
        if ($this->getUser()->isRoot()) {
            return true;
        }
        //Ha owner
        elseif($this->getUser()->getUserId() === $object->getOwnerId()) {
            return $object->getAccessRules(AccessManager::OWNER_USER)[$access_type];
        }
        //Ha csoport tag
        elseif (in_array($object->getOwnerGroupId(),$this->getUser()->getGroups())) {
            return $object->getAccessRules(AccessManager::GROUP_USER)[$access_type];
        }
        else {
            return $object->getAccessRules(AccessManager::OTHER_USER)[$access_type];
        }
    }

    /**
     * @param AccessibleObject $object
     * @return bool
     */
    public function checkReadAccess(AccessibleObject $object) {
        return $this->checkAccess($object,AccessManager::TYPE_READ);
    }

    /**
     * @param AccessibleObject $object
     * @return bool
     */
    public function checkWriteAccess(AccessibleObject $object) {
        return $this->checkAccess($object,AccessManager::TYPE_WRITE);
    }
}