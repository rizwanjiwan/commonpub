<?php

namespace rizwanjiwan\common\classes;

use Countable;
use Iterator;
use rizwanjiwan\common\interfaces\Nameable;
use rizwanjiwan\common\web\fields\AbstractField;

class FieldContainer implements  Iterator, Countable, Nameable
{
    private NameableContainer $container;
    /**
     * Nice way to chain calls easily through using this as a constructor
     * @param null|string $uniqueName provide a unique name to override the default and use this as a Nameable
     * @param null|string $friendlyName provide a friendly name to override the default and use this as a Nameable
     * @return FieldContainer to use
     */
    public static function create(?string $uniqueName=null,?string $friendlyName=null):FieldContainer
    {
        return new static($uniqueName,$friendlyName);
    }
    /**
     * NameableContainer constructor.
     * @param null|string $uniqueName provide a unique name to override the default and use this as a Nameable
     * @param null|string $friendlyName provide a friendly name to override the default and use this as a Nameable
     */
    public function __construct(?string $uniqueName=null,?string $friendlyName=null)
    {
        $this->container=new NameableContainer($uniqueName,$friendlyName);
    }

    /**
     * Add a Nameable
     * @param bool $overwrite true to allow overwriting if something already exists with the same name
     * @return $this to allow for chainable adds.
     */
    public function add(?AbstractField $field,bool $overwrite=false):self
    {
        $this->container->add($field,$overwrite);
        return $this;
    }
    /**
     * Remove a Nameable
     * @param $field AbstractField
     * @return $this to allow for chainable removes.
     */
    public function remove(AbstractField $field):self
    {
        $this->container->remove($field);
        return $this;
    }
    /**
     * Merge another container into this container
     * @param $container FieldContainer to merge in
     */
    public function merge(FieldContainer $container):void
    {
        foreach($container as $nameable)
            $this->add($nameable);
    }

    /**
     * Get the names of the items contained in this datastructures
     * @return string[]
     */
    public function getNames():array
    {
        return $this->container->getNames();
    }
    /**
     * Find out if this contains a given element
     * @param $name string name to check for
     * @return true if it is in here
     */
    public function contains(string $name):bool
    {
        return $this->container->contains($name);
    }
    /**
     * Get back a stored object
     * @param $name string the name of the item you want
     * @return AbstractField|null The stored Nameable or null if not found
     */
    public function get(string $name):?AbstractField
    {
        $field=$this->container->get($name);
        /**@var $field AbstractField**/
        return $field;
    }
    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return AbstractField
     * @since 5.0.0
     */
    public function current():AbstractField
    {
        $field=$this->container->current();
        /**@var $field AbstractField**/
        return $field;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        $this->container->next();
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key():int
    {
        return $this->container->key();
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be cast to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid():bool
    {
        return $this->container->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        $this->container->rewind();
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count():int
    {
        return $this->container->count();
    }

    /**
     * Sort the items of this container by the unique name
     */
    public function sort(): void
    {
        $this->container->sort();
    }

    public function getFriendlyName(): string
    {
        return $this->container->getFriendlyName();
    }

    public function getUniqueName(): string
    {
        return $this->container->getUniqueName();
    }
    public function getNameableContainer(): NameableContainer
    {
        return $this->container;
    }
}