<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 14.
 * Time: 21:46
 */

namespace PandaBase\Connection\Scheme;


use PandaBase\Exception\TableNotExists;


/**
 * Class TableDescriptor
 *
 * The class stores necessary information about a database table which has been mapped as php objects.
 *
 * @package PandaBase\Connection
 */
class Table {

    const TABLE_NAME        = "table_name";
    const TABLE_ID          = "table_id";
    const TABLE_SEQ_ID      = "table_seq_id";
    const LAZY_ATTRIBUTES   = "lazy_attributes";

    /**
     * @var array
     */
    private $descriptor;

    /**
     * @var string[]
     */
    private $allLazyAttributeNames;

    /**
     * Constructor
     *
     * @param $descriptors
     */
    public function __construct($descriptors) {
        $this->descriptor = $descriptors;
        $this->allLazyAttributeNames = null;
    }

    /**
     * It returns with the desired descriptor parameter if it exists. Otherwise throws TableDescriptorNotExists exception.
     *
     * @param string $descriptorKey
     * @return mixed
     * @throws TableNotExists
     */
    public function get($descriptorKey) {
        if(!array_key_exists($descriptorKey,$this->descriptor)) {
            throw new TableNotExists("Descriptor ".$descriptorKey." not exists");
        }
        return $this->descriptor[$descriptorKey];
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function isLazyAttribute(string $attributeName): bool {
        return
            isset($this->descriptor[Table::LAZY_ATTRIBUTES]) &&
            isset($this->descriptor[Table::LAZY_ATTRIBUTES][$attributeName]);
    }

    /**
     * @param string $attributeName
     * @return LazyAttribute
     */
    public function getLazyAttribute(string $attributeName): LazyAttribute {
        return $this->descriptor[Table::LAZY_ATTRIBUTES][$attributeName];
    }

    /**
     * @return string[]
     */
    public function getAllLazyAttributeNames() {
        if($this->allLazyAttributeNames != null) {
            return $this->allLazyAttributeNames;
        }
        if(!isset($this->descriptor[Table::LAZY_ATTRIBUTES])) {
            $this->allLazyAttributeNames = [];
            return [];
        }
        $this->allLazyAttributeNames = array_keys($this->descriptor[Table::LAZY_ATTRIBUTES]);
        return $this->allLazyAttributeNames;
    }
} 