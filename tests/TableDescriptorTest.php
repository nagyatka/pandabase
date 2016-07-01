<?php
use PandaBase\Connection\TableDescriptor;

class TableDescriptorTest extends PHPUnit_Framework_TestCase {

    public function testTableDescriptorInitialization() {
        $tableDescriptor = new TableDescriptor(array(
            TableDescriptor::TABLE_NAME   =>  "my_table_name",
            TableDescriptor::TABLE_ID     =>  "my_table_id",
            TableDescriptor::TABLE_SEQ_ID =>  "my_table_seq_id"
        ));

        $this->assertEquals($tableDescriptor->get(TableDescriptor::TABLE_NAME),"my_table_name");
        $this->assertEquals($tableDescriptor->get(TableDescriptor::TABLE_ID),"my_table_id");
        $this->assertEquals($tableDescriptor->get(TableDescriptor::TABLE_SEQ_ID),"my_table_seq_id");
    }

    /**
     * @expectedException Pandabase\Exception\TableDescriptorNotExists
     */
    public function testNotExistingDescriptorKey() {
        $tableDescriptor = new TableDescriptor(array());
        $tableDescriptor->get("not_existing_key");
    }

}
 