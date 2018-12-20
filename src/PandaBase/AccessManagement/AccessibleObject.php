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
     * @return array|null
     */
    public abstract function getReadAccessGroups(): ?array;

    /**
     * Returns with those group identifiers which have a write access to the given object.
     *
     * @return array|null
     */
    public abstract function getWriteAccessGroups(): ?array;
    
}