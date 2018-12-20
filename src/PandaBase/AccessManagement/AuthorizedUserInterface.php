<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2016. 08. 14.
 * Time: 12:00
 */

namespace PandaBase\AccessManagement;


interface AuthorizedUserInterface
{
    /**
     * The user has root privileges or not?
     *
     * @return bool
     */
    public function isRoot();

    /**
     * Returns with array of access group ids of the user.
     *
     * @return array
     */
    public function getAccessGroups();
}