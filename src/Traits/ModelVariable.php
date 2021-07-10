<?php
namespace Furo\Traits;

use Exception;

/**
 * Add validation class
 */
// use Furo\Entities\Valid;

/**
 * Create and validate object from $_POST request or PDO fetch class (public variables)
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
 *			$this->columns(['name','username','email','price']);
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
 * // Get data
 * $o = new SampleModel();
 * $user = $o->get(1);
 * $list = $o->all();
 *
 * // Examples
 * $o->columns(['username','name'])->table('addon')->limit(25,0);
 * $o->where('username', 'ben', '!=')->search('admin');
 * $o->range_out('price',26.0,96.0);
 *
 * $o->desc();
 * $o->update(['name' => 'ADMIN'], $user->id);
 * $o->select("user.*, user_token.token");
 * $o->join("LEFT JOIN user_token ON user.id = user_token.user_id");
 */
trait ModelVariable
{
	/**
	 * Add object variables
	 */
	// protected $email;

	/**
	 * Add object variable validation, when error throw exception
	 */
	// protected function email($str)
	// {
	// 	Valid::email($str);
	// }

	/**
	 * Allowed variables list for insert
	 *
	 * @var array
	 */
	public $model_variables = [];

	/**
	 * Get validated variables
	 *
	 * @return array Variables array
	 */
	final public function variables() {
		return $this->model_variables;
	}

	/**
	 * __get()
	 * Is utilized for reading data from inaccessible
	 * (protected or private) or non-existing properties.
	 */
	final public function __get($name)
	{
		if (property_exists($this, $name)) {
			return $this->model_variables[$name];
		}
	}

	/**
	 *	__set()
	 *  Is run when writing data to inaccessible
	 * (protected or private) or non-existing properties.
	 */
	final public function __set($name, $value)
	{
		// Secure variables
		if (preg_match('/^model_/i', $name) == 0) {
			// If exists
			if (property_exists($this, $name)) {
				// Validate
				if (method_exists($this, $name)) {
					$this->$name($value);
				}
				// Set
				$this->model_variables[$name] = $value;
			}
		} else {
			throw new Exception("ERR_PROPERTY_NAME_FORBIDDEN", 400);
		}
	}

	/**
	 * __call()
	 * Is triggered when invoking inaccessible
	 * methods in an object context.
	 */
	final public function __call($name, $args) {
		if (property_exists($this, $name)) {
			return $this->$name;
		}
	}

	/**
	 * __callStatic()
	 * Is triggered when invoking inaccessible static
	 * methods in an object context.
	 */
	final public function __callStatic($name, $args) {
		if (property_exists($this, $name)) {
			return $this->$name;
		}
	}
}