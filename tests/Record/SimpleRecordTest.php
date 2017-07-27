<?php
use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\Scheme\Table;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 07. 25.
 * Time: 12:20
 */
class SimpleRecordTest extends TestCase
{

    public function testConnectionInitializationWithTableDescriptor() {

        $testClassADescriptor = new Table([
            Table::TABLE_NAME => "pp_simple_table",
            Table::TABLE_ID   => "table_id",
            Table::LAZY_ATTRIBUTES => [
                "sub_table" => new \PandaBase\Connection\Scheme\LazyAttribute("table_id",TestClassB::class)
            ]
        ]);

        ConnectionManager::getInstance()->initializeConnection([
            "name"      =>  "test_connection",
            "driver"    =>  "mysql",
            "dbname"    =>  "phppuli",
            "host"      =>  "localhost",
            "user"      =>  "root",
            "password"  =>  "",
            "table_descriptors" => [
                TestClassA::class => $testClassADescriptor,
                TestClassB::class => $testClassADescriptor
            ]
        ]);

        $getTableDesc = ConnectionManager::getInstance()
            ->getConnection()
            ->getConnectionConfiguration()
            ->getTableDescriptor(TestClassA::class);

        $this->assertEquals($testClassADescriptor,$getTableDesc);
    }

    /**
     * @depends testConnectionInitializationWithTableDescriptor
     */
    public function testInitSimpleRecord() {

        $testObj = new TestClassA([
            "table_col_1" => "asdads"
        ]);

        $this->assertEquals([
            "table_col_1" => "asdads"
        ],$testObj->getAll());

        $this->assertEquals(ConnectionManager::getInstance()
            ->getConnection()
            ->getConnectionConfiguration()
            ->getTableDescriptor(TestClassA::class),$testObj->getTableDescriptor());

        $this->assertInstanceOf(TestClassB::class,$testObj["sub_table"]);
    }
}


class TestClassA extends \PandaBase\Record\SimpleRecord {

}

class TestClassB extends \PandaBase\Record\SimpleRecord {
    public function foo() {
        return $this["table_id"];
    }
}