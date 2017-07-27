<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 07. 25.
 * Time: 10:35
 */

namespace PandaBase\Connection\Scheme;


class LazyAttribute
{
    /**
     * @var string
     */
    private $foreign_key;

    /**
     * @var string
     */
    private $class;

    /**
     * LazyAttribute constructor.
     * @param $foreign_key
     * @param string $class
     */
    public function __construct( $foreign_key, $class)
    {
        $this->foreign_key = $foreign_key;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getForeignKey(): string
    {
        return $this->foreign_key;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }


}