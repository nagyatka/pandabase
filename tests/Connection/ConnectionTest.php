<?php
use PandaBase\Connection\{Connection,ConnectionConfiguration};
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase  {

    private $connection;

    public function __construct() {
        $this->connection = new Connection(
            ConnectionConfiguration::generateConfiguration([
                "name"      =>  "test_connection",
                "driver"    =>  "mysql",
                "dbname"    =>  "phppuli",
                "host"      =>  "localhost",
                "user"      =>  "root",
                "password"  =>  ""
            ])
        );
        date_default_timezone_set("Europe/Budapest");
        parent::__construct();
    }

    /**
     *
     */
    public function testConnection() {
        $this->assertInstanceOf(Connection::class,$this->connection);
        $this->assertInstanceOf("\\PDO",$this->connection->getDatabase());
    }

    /**
     *
     */
    public function testFetchAssoc() {
        $sql = "SELECT * FROM pp_simple_table WHERE table_id = :table_id";
        $params = [
            "table_id"  =>  1
        ];
        $result = $this->connection->fetchAssoc($sql,$params);
        $this->assertEquals(1,$result["table_id"]);
        $this->assertEquals("test_value_for_fetch",$result["table_col_1"]);
    }

    /**
     *
     */
    public function testFetchAll() {
        $sql = "SELECT * FROM pp_simple_table WHERE table_col_1= :col_1 LIMIT 2";
        $params = [
            "col_1" => "test_value_for_fetch"
        ];
        $result = $this->connection->fetchAll($sql,$params);
        $this->assertEquals(2,count($result));
    }
}














