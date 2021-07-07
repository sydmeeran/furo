<?php
namespace Furo\Traits;

/**
 * Add validation class
 */
// use Furo\Entities\Valid;

/**
 * Create and validate object from $_POST request or PDO fetch class
 *
 * use Furo\Traits\ModelVariable;
 * use Furo\Traits\ModelDatabase;
 *
 * $_POST['email'] = 'sample@email.com';
 *
 * class SampleModel
 * {
 * 		trait ModelVariable; // Validation
 * 		trait ModelDatabase; // Model methods (optional)
 *
 * 		protected $email;
 *
 * 		protected function email($str) {
 * 			// Validate email here, on error throw exception
 * 			// throw new Exception('ERR_EMAIL', 400);
 * 		}
 *
 * 		function __construct()
 *		{
 *			$this->table('user');
 *			$this->columns(['name','username','email', 'price']);
 *		}
 *
 *		function add(array $arr)
 *		{
 *			// Override this method
 *			throw new Exception("ERR_ADD_METHOD", 400);
 *			return 0;
 *		}
 * }
 *
 * $o = new SampleModel();
 *
 * // Validate
 * foreach($_POST as $property => $value) {
 * 		$o->$property = $value;
 * }
 *
 * // Properties
 * echo $o->email;
 * echo $o->email();
 *
 * // Db
 * $o->columns(['username','name'])->table('addon')->limit(12,1);
 * $o->where('username', 'ben', '!=')->search('adm');
 * $o->range_out('price',26.0,96.0);
 * print_r($o->all());
 * $user = $o->get(1);
 *
 * // $o->desc();
 * // $o->update(['name' => 'ADMIN'], 321);
 * // $o->select("user.*, user_token.token");
 * // $o->join("LEFT JOIN user_token ON user.id = user_token.user_id");
 */
trait ModelVariable
{
	/**
	 * Add object variables
	 */
	// protected $username;
	// protected $email;
	// protected $time;
	// protected $code;
	// protected $role;
	// protected $name;
	// protected $about;
	// protected $www;
	// protected $location;

	/**
	 * Add object property validation, when error throw exception
	 */
	// protected function username($str)
	// {
	// 	Valid::alias($str);
	// }

	/**
	 * Add object property validation, when error throw exception
	 */
	// protected function email($str)
	// {
	// 	Valid::email($str);
	// }

	/**
	 * __get()
	 * Is utilized for reading data from inaccessible
	 * (protected or private) or non-existing properties.
	 */
	public function __get($name)
	{
		if (property_exists($this, $name)) {
			return $this->$name;
		}
	}

	/**
	 *	__set()
	 *  Is run when writing data to inaccessible
	 * (protected or private) or non-existing properties.
	 */
	public function __set($name, $value)
	{
		if (property_exists($this, $name)) {
			// Validate
			$this->$name($value);
			// Set
			$this->$name = $value;
		}
	}

	/**
	 * __call()
	 * Is triggered when invoking inaccessible
	 * methods in an object context.
	 */
	public function __call($name, $args) {
		if (property_exists($this, $name)) {
			return $this->$name;
		}
	}
}