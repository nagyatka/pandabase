<?php

namespace PandaBase\AccessManagement;


use PandaBase\Exception\AccessDeniedException;

class AccessManager
{
    const TYPE_EXEC = 8;
    const TYPE_READ = 4;
    const TYPE_WRITE= 2;

    const OWNER_USER = "owner";
    const GROUP_USER = "group";
    const OTHER_USER = "other";
    const ANON_USER  = "anon";

    /**
     * @var null|AuthorizedUserInterface
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
     * @param AuthorizedUserInterface $accessUser
     * @return void
     */
    public function registerUser(AuthorizedUserInterface $accessUser) {
        $this->accessUser = $accessUser;
    }

    /**
     * @return null|AuthorizedUserInterface
     */
    public function getUser() {
        return $this->accessUser;
    }

    /**
     *
     *
     * @param AccessibleObject $object
     * @param int $access_type
     * @return bool
     * @throws AccessDeniedException
     */
    private function checkAccess(AccessibleObject $object, $access_type) {

        if($this->getUser() == null) {
            throw new AccessDeniedException("Missing Authorized user.");
        }

        $authUser = $this->getUser();

        if ($this->getUser()->isRoot()) {
            return true;
        }

        $userAccessGroups = $authUser->getAccessGroups();

        switch ($access_type) {
            case AccessManager::TYPE_READ:
                $objectAccessGroups = $object->getReadAccessGroups();
                break;
            case AccessManager::TYPE_WRITE:
                $objectAccessGroups = $object->getWriteAccessGroups();
                break;
            default:
                throw new AccessDeniedException("Unknown access type");
        }

        foreach ($userAccessGroups as $accessGroup) {
            if(in_array($accessGroup, $objectAccessGroups)) {
                return true;
            }
        }
        return false;
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