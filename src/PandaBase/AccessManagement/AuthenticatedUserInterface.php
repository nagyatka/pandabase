<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2016. 08. 14.
 * Time: 12:00
 */

namespace PandaBase\AccessManagement;


interface AuthenticatedUserInterface
{
    /**
     * The user has root privileges or not?
     *
     * @return bool
     */
    public function isRoot();

    /**
     * Returns with the unique id of the user.
     *
     * @return int
     */
    public function getUserId();

    /**
     * Returns with array of group ids of the user.
     *
     * @return array
     */
    public function getGroups();
}