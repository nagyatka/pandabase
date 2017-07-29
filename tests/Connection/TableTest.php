<?php
use PandaBase\Connection\Scheme\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase {

    public function testTableInitialization() {
        $tableDescriptor = new Table(array(
            Table::TABLE_NAME   =>  "my_table_name",
            Table::TABLE_ID     =>  "my_table_id",
            Table::TABLE_SEQ_ID =>  "my_table_seq_id"
        ));

        $this->assertEquals($tableDescriptor->get(Table::TABLE_NAME),"my_table_name");
        $this->assertEquals($tableDescriptor->get(Table::TABLE_ID),"my_table_id");
        $this->assertEquals($tableDescriptor->get(Table::TABLE_SEQ_ID),"my_table_seq_id");
    }

    /**
     * @expectedException Pandabase\Exception\TableNotExists
     */
    public function testNotExistingDescriptorKey() {
        $tableDescriptor = new Table(array());
        $tableDescriptor->get("not_existing_key");
    }

}
 