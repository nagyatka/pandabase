<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 14.
 * Time: 21:46
 */

namespace PandaBase\Connection;


use PandaBase\Exception\TableDescriptorNotExists;



define("TABLE_NAME","table_name");
define("TABLE_ID","table_id");
define("TABLE_SEQ_ID","table_seq_id");

class TableDescriptor {

    const TABLE_NAME    = "table_name";
    const TABLE_ID      = "table_id";
    const TABLE_SEQ_ID  = "table_seq_id";

    /**
     * @var array
     */
    private $descriptor;

    /**
     * Konstruktor
     *
     * @param $descriptors
     */
    public function __construct($descriptors) {
        $this->descriptor  =   $descriptors;
    }

    /**
     * Visszaadja a táblaleíró paraméterben kért elemét.
     *
     * @param string $descriptorKey
     * @return mixed
     * @throws TableDescriptorNotExists
     */
    public function get($descriptorKey) {
        if(!array_key_exists($descriptorKey,$this->descriptor)) {
            throw new TableDescriptorNotExists("Descriptor ".$descriptorKey." not exists");
        }
        return $this->descriptor[$descriptorKey];
    }



} 