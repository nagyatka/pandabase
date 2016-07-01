<?php
use PandaBase\Connection\ConnectionConfiguration;

/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 02. 27.
 * Time: 15:41
 */

class ConnectionConfigurationTest extends PHPUnit_Framework_TestCase {
    public function testConfigurationGeneration() {
        $config = ConnectionConfiguration::generateConfiguration([
            "name"      =>  "test_connection",
            "driver"    =>  "mysql",
            "dbname"    =>  "phppuli_test",
            "host"      =>  "127.0.0.1",
            "user"      =>  "root",
            "password"  =>  ""
        ]);
        $this->assertInstanceOf(ConnectionConfiguration::class,$config);
    }

    public function testGetters() {
        $config = \PandaBase\Connection\ConnectionConfiguration::generateConfiguration([
            "name"      =>  "test_connection",
            "driver"    =>  "mysql",
            "dbname"    =>  "phppuli_test",
            "host"      =>  "127.0.0.1",
            "user"      =>  "root",
            "password"  =>  ""
        ]);

        $this->assertEquals($config->getName(),"test_connection");
        $this->assertEquals($config->getHost(),"127.0.0.1");
        $this->assertEquals($config->getDbname(),"phppuli_test");
        $this->assertEquals($config->getDriver(),"mysql");
        $this->assertEquals($config->getUser(),"root");
    }
}
 