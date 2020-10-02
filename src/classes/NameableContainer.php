<?php
/**
 * Allows itteration over a bunch of objects easily
 */

namespace rizwanjiwan\common\classes;

use rizwanjiwan\common\classes\exceptions\NameableException;
use rizwanjiwan\common\interfaces\Nameable;
use rizwanjiwan\common\traits\NameableTrait;

class NameableContainer implements \Iterator, \Countable, Nameable
{
	use NameableTrait;
	/**
	 * @var Nameable[] with the index being the string name
	 */
	private $items=array();

	/**
	 * @var int index into $keys for iterating
	 */
	private $index=0;

	/**
	 * @var string[] Keys for $items
	 */
	private $keys=array();

	/**
	 * Nice way to chain calls easily through using this as a constructor
	 * @param null|string $uniqueName provide a unique name to override the default and use this as a Nameable
	 * @param null|string $friendlyName provide a friendly name to override the default and use this as a Nameable
	 * @return NameableContainer to use
	 */
	public static function create($uniqueName=null,$friendlyName=null)
	{
		return new static($uniqueName,$friendlyName);
	}

	/**
	 * NameableContainer constructor.
	 * @param null|string $uniqueName provide a unique name to override the default and use this as a Nameable
	 * @param null|string $friendlyName provide a friendly name to override the default and use this as a Nameable
	 */
	public function __construct($uniqueName=null,$friendlyName=null)
	{
		if($uniqueName!==null)
		{
			try
			{
				$this->setUniqueName($uniqueName);
			}
			catch(NameableException $e)//not possible but we need to catch
			{
				$log=LogManager::createLogger();
				$log->error($e->getMessage()." -> ".$e->getTraceAsString());
			}
		}
		if($friendlyName!==null)
			$this->setFriendlyName($friendlyName);
	}

	/**
	 * Add a Namable
	 * @param $nameable Nameable
	 * @param bool $overwrite true to allow overwriting if something already exists with the same name
	 * @return $this to allow for chainable adds.
	 */
	public function add($nameable,$overwrite=false)
	{
		if($nameable===null)
			return $this;// don't add nulls
		$uniqueName=$nameable->getUniqueName();
		//$log = Logger::getLogger('default');
		//$log->trace('adding '.$uniqueName.' to container');
		$this->items[$uniqueName]=$nameable;
		if(($overwrite===false)&&(array_key_exists($uniqueName,$this->items)))	//prevent multiple adds of the same thing to the key list
			return $this;
		array_push($this->keys,$uniqueName);
		return $this;
	}
	/**
	 * Remove a Namable
	 * @param $nameable Nameable
	 * @return $this to allow for chainable removes.
	 */
	public function remove($nameable)
	{
		$uniqueName=$nameable->getUniqueName();
		if(!array_key_exists($uniqueName,$this->items))	//only remove things we actually have
			return $this;
		unset($this->items[$uniqueName]);
		$this->rewind();
		return $this;
	}
	/**
	 * Merge another NameableContainer into this container
	 * @param $container NameableContainer to merge in
	 */
	public function merge($container)
	{
		foreach($container as $namable)
			$this->add($namable);
	}

	/**
	 * Get the names of the items contained in this datastructure
	 * @return string[]
	 */
	public function getNames()
	{
		return array_keys($this->items);
	}
	/**
	 * Find out if this contains a given element
	 * @param $name string name to check for
	 * @return true if it is in here
	 */
	public function contains($name)
	{
		return array_key_exists($name,$this->items);
	}
	/**
	 * Get back a stored object
	 * @param $name string the name of the item you want
	 * @return Nameable|null The stored Nameable or null if not found
	 */
	public function get($name)
	{
		if($this->contains($name))
			return $this->items[$name];
		return null;
	}
	/**
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return Nameable
	 * @since 5.0.0
	 */
	public function current()
	{
		return $this->items[$this->keys[$this->index]];
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		$this->index++;
	}

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return $this->index;
	}

	/**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid()
	{
		return $this->index<count($this->keys);
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind()
	{

		$this->keys = $this->getNames();
		$this->index=0;
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
	public function count()
	{
		return count($this->items);
	}

	/**
	 * Sort the items of this container by the unique name
	 */
	public function sort()
	{
		ksort($this->items);
		sort($this->keys);
	}
}