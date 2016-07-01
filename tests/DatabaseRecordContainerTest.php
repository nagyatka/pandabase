<?php
use PandaBase\Record\DatabaseRecordContainer;
use PandaBase\Record\MixedRecord;

/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 19.
 * Time: 0:11
 */

class DatabaseRecordContainerTest extends PHPUnit_Framework_TestCase {


    public function testForeach() {
        $container = new \PandaBase\Record\DatabaseRecordContainer(array(
            new \PandaBase\Record\MixedRecord(array("val_1"=>1)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>2)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>3)),
        ));


        $container->foreachRecords(function (\PandaBase\Record\DatabaseRecord $record) {
            $record["x"] = 5;
        });
        $tester = $this;
        $container->foreachRecords(function (\PandaBase\Record\DatabaseRecord $record) use ($tester) {
            $tester->assertEquals(5,$record["x"]);
        });
    }

    public function testFilter() {
        $container = new \PandaBase\Record\DatabaseRecordContainer(array(
            new \PandaBase\Record\MixedRecord(array("val_1"=>-4)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>2)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>3)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>1)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>10)),
        ));

        $tester = $this;
        $container->filter( function($record){
            if($record["val_1"] > 1) {
                return true;
            }
            else {
                return false;
            }
        })->foreachRecords(function (\PandaBase\Record\DatabaseRecord $record) use ($tester) {
            $tester->assertGreaterThan(1,$record["val_1"]);
        });
    }

    public function testMap() {
        $container = new \PandaBase\Record\DatabaseRecordContainer(array(
            new \PandaBase\Record\MixedRecord(array("val_1"=>"a")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"b")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"c")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"d")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"e")),
        ));
        $tester = $this;
        $container = $container->map(function(\PandaBase\Record\DatabaseRecord $record){
            return $record["val_1"].'mapped';
        });
        $container->foreachRecords(function($row) use ($tester) {
            $tester->assertRegExp('/mapped/',$row);
        });
    }

    public function testMapReduce() {
        $container = new \PandaBase\Record\DatabaseRecordContainer(array(
            new \PandaBase\Record\MixedRecord(array("val_1"=>"a")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"bs")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"cd")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"daa")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"edddd")),
        ));
        $result = $container->map(function(\PandaBase\Record\DatabaseRecord $record){
            return strlen($record["val_1"]);
        })->reduce(function($row1,$row2){
            return $row1 + $row2;
        });
        $this->assertEquals(13,$result);
    }

    public function testFindBy() {
        $container = new \PandaBase\Record\DatabaseRecordContainer(array(
            new \PandaBase\Record\MixedRecord(array("val_1"=>-4)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>2)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>3)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>1)),
            new \PandaBase\Record\MixedRecord(array("val_1"=>10)),
        ));
        $record = $container->findBy("val_1",2);
        $this->assertNotEquals(null,$record);
        $record = $container->findBy("val_1",-2);
        $this->assertEquals(null,$record);
    }


    public function testGet() {
        $container = new \PandaBase\Record\DatabaseRecordContainer(array(
            new \PandaBase\Record\MixedRecord(array("val_1"=>"a")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"bs")),
            new \PandaBase\Record\MixedRecord(array("val_1"=>"cd")),
            new MixedRecord(array("val_1"=>"daa")),
            new MixedRecord(array("val_1"=>"edddd")),
        ));

        $this->assertEquals("a",$container[0]["val_1"]);
        $this->assertEquals("bs",$container->get(1)["val_1"]);
    }

    public function testSortBy() {
        $container = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a")),
            new MixedRecord(array("val_1"=>"edddd")),
            new MixedRecord(array("val_1"=>"bs")),
            new MixedRecord(array("val_1"=>"cd")),
            new MixedRecord(array("val_1"=>"daa")),
        ));

        $this->assertEquals("a",$container->sortBy("val_1",SORT_ASC)[0]["val_1"]);
        $this->assertEquals("edddd",$container->sortBy("val_1",SORT_DESC)[0]["val_1"]);
    }

    public function testJoin() {
        $container1 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_2"=>1)),
            new MixedRecord(array("val_1"=>"b","val_2"=>2)),
        ));
        $container2 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_3"=>3)),
            new MixedRecord(array("val_1"=>"a","val_3"=>4)),
            new MixedRecord(array("val_1"=>"b","val_3"=>3)),
        ));

        $this->assertEquals(3,$container1->join("val_1",$container2)->size());

        $container1 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_2"=>1)),
            new MixedRecord(array("val_1"=>"b","val_2"=>2)),
            new MixedRecord(array("val_1"=>"c","val_2"=>2)),
        ));
        $container2 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_3"=>3)),
            new MixedRecord(array("val_1"=>"a","val_3"=>4)),
            new MixedRecord(array("val_1"=>"b","val_3"=>3)),
        ));
        $this->assertEquals(3,$container1->join("val_1",$container2)->size());
    }

    public function testLeftJoin() {
        $container1 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_2"=>1)),
            new MixedRecord(array("val_1"=>"b","val_2"=>2)),
        ));
        $container2 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_3"=>3)),
            new MixedRecord(array("val_1"=>"a","val_3"=>4)),
            new MixedRecord(array("val_1"=>"b","val_3"=>3)),
        ));

        $this->assertEquals(3,$container1->leftJoin("val_1",$container2)->size());

        $container1 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_2"=>1)),
            new MixedRecord(array("val_1"=>"b","val_2"=>2)),
            new MixedRecord(array("val_1"=>"c","val_2"=>2)),
        ));
        $container2 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_3"=>3)),
            new MixedRecord(array("val_1"=>"a","val_3"=>4)),
            new MixedRecord(array("val_1"=>"b","val_3"=>3)),
        ));

        $this->assertEquals(4,$container1->leftJoin("val_1",$container2)->size());
    }

    /**
     * @expectedException \PandaBase\Exception\RecordValueNotExists
     */
    public function testRightJoin() {
        $container1 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_2"=>1)),
            new MixedRecord(array("val_1"=>"b","val_2"=>2)),
        ));
        $container2 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_3"=>3)),
            new MixedRecord(array("val_1"=>"a","val_3"=>4)),
            new MixedRecord(array("val_1"=>"b","val_3"=>3)),
        ));

        $this->assertEquals(3,$container1->rightJoin("val_1",$container2)->size());

        $container1 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_2"=>1)),
            new MixedRecord(array("val_1"=>"b","val_2"=>2)),
            new MixedRecord(array("val_1"=>"c","val_2"=>2)),
        ));
        $container2 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_3"=>3)),
            new MixedRecord(array("val_1"=>"a","val_3"=>4)),
            new MixedRecord(array("val_1"=>"b","val_3"=>3)),
            new MixedRecord(array("val_1"=>"c","val_3"=>2)),
            new MixedRecord(array("val_1"=>"w","val_3"=>2)),
        ));
        $res = $container1->rightJoin("val_1",$container2);
        $this->assertEquals(5,$res->size());
        $valasd = $res[4]["val_2"];
    }

    /**
     * @expectedException \PandaBase\Exception\NotDatabaseRecordInstanceException
     */
    public function testSetRecord() {
        $container1 = new DatabaseRecordContainer(array(
            new MixedRecord(array("val_1"=>"a","val_2"=>1)),
            new MixedRecord(array("val_1"=>"b","val_2"=>2)),
        ));
        $container1[0] = ["val_1"=>5];
    }



}
 