<?php


namespace rizwanjiwan\common\traits;


use rizwanjiwan\common\classes\exceptions\NameableException;

trait NameableTrait
{

	private $name=null;
	private $friendlyName=null;

	/**
	 * A friendly name for the end user to see
	 * @return string friendly name
	 */
	public function getFriendlyName()
	{
		if($this->friendlyName===null)
		{
			$this->friendlyName=$this->getUniqueName();
		}
		return $this->friendlyName;
	}

	/**
	 * A name for use that is unique
	 * @return string name
	 */
	public function getUniqueName()
	{
		if($this->name==null)
		{
			//just use class name as a fail safe
			/*$class=get_class($this);
			if (preg_match('@\\\\([\w]+)$@', $class, $matches))
				$this->name=$matches[1];*/
			$this->name=get_class($this);
		}
		return $this->name;
	}

	/**
	 * Provide a friendly name to use
	 * @param $name string
	 */
	protected function setFriendlyName($name)
	{
		$this->friendlyName=$name;
	}

	/**
	 * Provide a name to use
	 * @param $name string
	 * @throws NameableException if name has already been set/used.
	 */
	protected function setUniqueName($name)
	{
		if($this->name!==null)
			throw new NameableException("Can't change name after it's been initially set/used");
		$this->name=$name;
	}
}