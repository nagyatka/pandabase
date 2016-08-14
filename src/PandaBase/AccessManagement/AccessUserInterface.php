<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2016. 08. 14.
 * Time: 12:00
 */

namespace PandaBase\AccessManagement;


interface AccessUserInterface
{
    /**
     * Az adott felhasználó rendelkezik-e root jogokkal.
     * @return bool
     */
    public function isRoot();

    /**
     * Az aktuális felhasználó egyedi azonosítója.
     * @return int
     */
    public function getUserId();
}