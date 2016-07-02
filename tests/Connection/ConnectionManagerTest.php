<?php
use PandaBase\Connection\Connection;
use PandaBase\Connection\ConnectionManager;
use PandaBase\Record\InstanceRecordContainer;
use PandaBase\Record\MixedRecordContainer;
use PandaBase\Test\TestRecord;

/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 02. 27.
 * Time: 17:28
 */

class ConnectionManagerTest extends PHPUnit_Framework_TestCase {

    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = ConnectionManager::getInstance();
        $this->connectionManager->emptyConnections();
    }

    public function testGetInstance() {
        $this->assertInstanceOf(ConnectionManager::class,$this->connectionManager);
    }

    public function testInitializeConnection() {
        $this->connectionManager->initializeConnection([
            "name"      =>  "test_connection",
            "driver"    =>  "mysql",
            "dbname"    =>  "phppuli",
            "host"      =>  "127.0.0.1",
            "user"      =>  "root",
            "password"  =>  ""
        ]);
        $this->assertInstanceOf(Connection::class,$this->connectionManager->getConnection());
        $this->assertInstanceOf(Connection::class,$this->connectionManager->getConnection("test_connection"));
        $this->assertInstanceOf(Connection::class,$this->connectionManager->getDefault());
    }

    /**
     * @expectedException PandaBase\Exception\ConnectionNotExistsException
     */
    public function testGetConnectionError() {
        $this->assertInstanceOf(Connection::class,$this->connectionManager->getConnection("asdasd"));
    }

    public function testSetGetDefault() {
        $this->connectionManager->initializeConnection([
            "name"      =>  "test_connection_2",
            "driver"    =>  "mysql",
            "dbname"    =>  "phppuli",
            "host"      =>  "127.0.0.1",
            "user"      =>  "root",
            "password"  =>  ""
        ]);
        $conn2 = md5(serialize($this->connectionManager->getConnection("test_connection_2")->getConnectionConfiguration()));
        $this->connectionManager->setDefault("test_connection_2");
        $conn2d = md5(serialize($this->connectionManager->getDefault()->getConnectionConfiguration()));
        $this->assertEquals($conn2,$conn2d);
    }

    public function testGetAllConnection() {
        $this->assertEquals(2,count($this->connectionManager->getAllConnection()));
    }

    /**
     * @expectedException PandaBase\Exception\ConnectionNotExistsException
     */
    public function testSetDefaultError() {
        $this->connectionManager->setDefault("test_connection_3");
    }


    public function testGetMixedRecords(){
        $sql = "SELECT * FROM pp_simple_table WHERE table_col_1= :col_1 LIMIT 2";
        $params = [
            "col_1" => "test_value_for_fetch"
        ];
        $result = $this->connectionManager->getMixedRecords($sql,$params);
        $this->assertInstanceOf(MixedRecordContainer::class,$result);
        $this->assertEquals(2,count($result));
    }

    public function testGetInstanceRecords() {
        $sql = "SELECT * FROM pp_simple_table WHERE table_col_1= :col_1 LIMIT 2";
        $params = [
            "col_1" => "test_value_for_fetch"
        ];
        $result = $this->connectionManager->getInstanceRecords(TestRecord::class,$sql,$params);
        $this->assertInstanceOf(InstanceRecordContainer::class,$result);
        $record1 = $result->findBy("table_id",1);
        $this->assertNotEquals(null,$record1);
    }

    public function testPersistEdit() {
        \PandaBase\Connection\ConnectionManager::getInstance()->initializeConnection([
            "name"      =>  "test_connection",
            "driver"    =>  "mysql",
            "dbname"    =>  "phppuli",
            "host"      =>  "localhost",
            "user"      =>  "root",
            "password"  =>  ""
        ]);
        \PandaBase\Connection\ConnectionManager::getInstance()->setDefault("test_connection");

        $testRecord = new TestRecord(1);
        $now = date('Y-m-d H:i:s');
        $testRecord["store_date"] = $now;
        $this->connectionManager->persist($testRecord);
        $testRecord = null;
        $testRecordNew = new TestRecord(1);
        $this->assertEquals($now,$testRecordNew["store_date"]);
    }

    public function testPersistNew() {
        $newRecord = new TestRecord(CREATE_INSTANCE,[
            "table_col_1"   =>  "add_record",
            "store_date"    =>  date('Y-m-d H:i:s')
        ]);
        $actualConnection = \PandaBase\Connection\ConnectionManager::getInstance()->getDefault();
        $this->connectionManager->persist($newRecord);
        $this->assertNotEquals(null,$newRecord[$newRecord->getTableDescriptor()->get(TABLE_ID)]);

    }

    public function testPersistAll() {
        $random = rand(0,100000000000);
        $res = $this->connectionManager->getInstance()->getInstanceRecords(
            TestRecord::class,
            "SELECT * FROM pp_simple_table WHERE table_col_1='add_record'");
        $res->foreachRecords(function(\PandaBase\Record\DatabaseRecord $record) use($random){
            $record["table_col_1"] = "persisted_all_".$random;
        });
        $this->connectionManager->getInstance()->persistAll($res);

        $res2 = $this->connectionManager->getInstance()->getInstanceRecords(
            TestRecord::class,
            "SELECT * FROM pp_simple_table WHERE table_col_1='persisted_all_".$random."'");

        $this->assertNotEquals(0,count($res2));
    }
}
 