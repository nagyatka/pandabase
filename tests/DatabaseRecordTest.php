<?php
use PandaBase\Record\MixedRecord;

/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 14.
 * Time: 22:28
 */

class DatabaseRecordTest extends PHPUnit_Framework_TestCase {

    public function testGetValues() {

        $mixedRecord = new MixedRecord(array(
            "test_key"  =>  "test_value"
        ));
        $this->assertEquals("test_value",$mixedRecord->get("test_key"));
        $this->assertEquals("test_value",$mixedRecord["test_key"]);
        $this->assertEquals(array("test_key"  =>  "test_value"),$mixedRecord->getAll());
    }

    public function testSetValues() {

        $mixedRecord = new MixedRecord(array(
            "test_key_1"  =>  "test1",
            "test_key_2"=>  "test2"
        ));
        $mixedRecord["test_key_1"] = "test_value_1";
        $this->assertEquals("test_value_1",$mixedRecord["test_key_1"]);
        $mixedRecord->set("test_key_2","test_value_2");
        $this->assertEquals("test_value_2",$mixedRecord["test_key_2"]);
        $mixedRecord->setAll(array(
            "test_key_1"    =>  "test1All",
            "test_key_2"    =>  "test2All"
        ));
        $this->assertEquals(array(
            "test_key_1"    =>  "test1All",
            "test_key_2"    =>  "test2All"
        ),$mixedRecord->getAll());

    }

    /**
     * @throws \PandaBase\Exception\RecordValueNotExists
     */
    public function testSetValuesWithException() {

        $mixedRecord = new MixedRecord(array(
            "test_key"  =>  "test_value"
        ));
        $this->assertEquals("test_value",$mixedRecord->get("test_key"));
        $this->assertEquals("test_value",$mixedRecord["test_key"]);
    }

} 