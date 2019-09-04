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

    /*
     * Necessary constants (fields) for all RecordHandlers
     */
    const TABLE_NAME        = "table_name";
    const TABLE_ID          = "table_id";
    const LAZY_ATTRIBUTES   = "lazy_attributes";

    /*
     * Necessary constants (fields) for handlers of tracked records
     */
    const TABLE_SEQ_ID      = "table_seq_id";
    const HISTORY_TABLE_NAME= "history_table";
    const HISTORY_FROM      = "history_from";
    const HISTORY_TO        = "history_to";
    const RECORD_STATUS     = "record_status";

    /*
     * The constant is responsible for store additional information about the table
     */
    const FIELDS            = "fields";
    const PRIMARY_KEY       = "primary_key";
    const ENGINE            = "engine";
    const INDEX             = "index";


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
     * @param mixed $default The desired value if the descriptor key is not exist
     * @return mixed
     */
    public function get($descriptorKey, $default = null) {
        if(!array_key_exists($descriptorKey,$this->descriptor)) {
            return $default;
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

    /**
     * If the Table::FIELDS key exists in the table descriptor it returns with the array of the fields. The structure of
     * the array is the following:
     *
     *  field_name => [
     *
     *
     * @return array|null
     */
    public function getFields() {
        if(!isset($this->descriptor[Table::FIELDS])) {
            return null;
        }
        return $this->descriptor[Table::FIELDS];
    }
} 