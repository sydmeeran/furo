<?php
namespace Furo\Entities;

use Furo\Entities\Valid;

/**
 * Create and validate object from $_POST request or PDO fetch class
 *
 * $u = new Test();
 * foreach($_POST as $key => $value) {
 * 		$u->$key = $value;
 * }
 */
class Entity
{
	protected $www;
	protected $time;
	protected $code;
	protected $role;
	protected $name;
	protected $email;
	protected $about;
	protected $username;
	protected $location;

	/**
	 * __get()
	 * Is utilized for reading data from inaccessible
	 * (protected or private) or non-existing properties.
	 */
	public function __get($key)
	{
		if (property_exists($this, $key)) {
			return $this->$key;
		}
	}

	/**
	 *	__set()
	 *  Is run when writing data to inaccessible
	 * (protected or private) or non-existing properties.
	 */
	public function __set($key, $value)
	{
		if (property_exists($this, $key)) {
			// Validate
			$this->$key($value);
			// Set
			$this->$key = $value;
		}
	}

	/**
	 * __call()
	 * Is triggered when invoking inaccessible
	 * methods in an object context.
	 */
	public function __call($name, $args)
    {
		// echo "Calling inaccessible object method $name ". implode(', ', $args). "\n";
    }

	/**
	 * Validate username
	 */
	protected function username($str)
	{
		Valid::alias($str);
	}

	/**
	 * Validate email
	 */
	protected function email($str)
	{
		Valid::email($str);
	}
}