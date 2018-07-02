<?php

namespace PandaBase\Connection\Scheme;

/**
 * Class LazyAttribute
 *
 *
 * @package PandaBase\Connection\Scheme
 */
class LazyAttribute
{
    const OneToOne = 0;
    const OneToMany= 1;

    /**
     * The column name of the foreign key.
     *
     * @var string
     */
    private $key;

    /**
     * The name of the the class which represents the other object/table.
     *
     * @var string
     */
    private $class;

    /**
     * The type of the connection (OneToOne or OneToMany).
     *
     * @var int
     */
    private $type;

    /**
     * OneToOneLazyAttribute constructor.
     * @param string $foreign_key
     * @param string $class
     * @param int $type
     */
    public function __construct(string $foreign_key, string $class, int $type)
    {
        $this->key = $foreign_key;
        $this->class = $class;
        $this->type = $type;
    }

    /**
     * Returns with the column name of the foreign key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns with the name of the other class.
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Returns with the type of the LazyAttribute
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
}