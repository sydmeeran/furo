<?php
namespace App\Model\Sample;

use Exception;
use App\Model\Model;
use Furo\Entities\Valid;

/**
 * Sample model
 */
class SampleModel extends Model
{
	protected $username;
	protected $email;
	protected $name;
	protected $about;
	protected $location;
	protected $www;

	protected function email($str)
	{
		Valid::email($str);
	}

	protected function username($str)
	{
		Valid::alias($str);
	}

	function add()
	{
		// Inactive in user
		try {
			// Do something
		} catch(Exception $e) {
			// Error
		}
	}
}