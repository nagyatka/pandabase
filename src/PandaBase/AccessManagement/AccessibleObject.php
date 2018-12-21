<?php

namespace PandaBase\AccessManagement;


/**
 * Trait AccessibleObject
 *
 *
 * @package PandaBase\AccessManagement
 */
trait AccessibleObject
{
    /**
     * Returns with those group identifiers which have a read access to the given object.
     *
     * @param int $user_id
     * @return bool
     */
    public abstract function checkReadAccess($user_id): bool;

    /**
     * Returns with those group identifiers which have a write access to the given object.
     *
     * @param int $user_id
     * @return bool
     */
    public abstract function checkWriteAccess($user_id): bool;
    
}