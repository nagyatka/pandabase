<?php

namespace PandaBase\AccessManagement;


use PandaBase\Exception\AccessDeniedException;

class AccessManager
{
    const TYPE_READ = 4;
    const TYPE_WRITE= 2;

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
    private function checkAccess($object, $access_type) {

        if($this->getUser() == null) {
            throw new AccessDeniedException("Missing Authorized user.");
        }

        $authUser = $this->getUser();

        if ($this->getUser()->isRoot()) {
            return true;
        }

        switch ($access_type) {
            case AccessManager::TYPE_READ:
                return $object->checkReadAccess($authUser->getUserId());
            case AccessManager::TYPE_WRITE:
                return $object->checkWriteAccess($authUser->getUserId());
                break;
            default:
                throw new AccessDeniedException("Unknown access type");
        }
    }

    /**
     * @param AccessibleObject $object
     * @return bool
     */
    public function checkReadAccess($object) {
        return $this->checkAccess($object,AccessManager::TYPE_READ);
    }

    /**
     * @param AccessibleObject $object
     * @return bool
     */
    public function checkWriteAccess($object) {
        return $this->checkAccess($object,AccessManager::TYPE_WRITE);
    }
}