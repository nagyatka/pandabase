<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 01. 16.
 * Time: 13:08
 */

namespace PandaBase\Record;


class MixedRecordHandler extends RecordHandler {
    function __construct()
    {
        parent::__construct(new TableDescriptor(array()));
    }

    /**
     * @return array
     */
    public function insert()
    {
        // TODO: Implement insert() method.
    }

    /**
     * @param int $id
     * @return array
     */
    public function select($id)
    {
        // TODO: Implement select() method.
    }


    public function edit()
    {
        // TODO: Implement edit() method.
    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }
}