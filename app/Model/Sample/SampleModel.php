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
	protected $id = 0;
	protected $username;
	protected $email;
	protected $name;
	protected $about;
	protected $location;
	protected $www;

	protected function id($id)
	{
		Valid::unsigned($id);
	}

	protected function email($str)
	{
		Valid::email($str);
	}

	protected function username($str)
	{
		Valid::alias($str);
	}

	function sampleMethod()
	{
		try
		{
			// Do something
		}
		catch(Exception $e)
		{
			throw new Exception("ERR_SAMPLE_METHOD", 400);
		}
	}
}

/*
// Model how to
$m = new SampleModel();

// Validate and set variables
foreach ($_POST as $k => $v) {
	$m->$k = $v;
}

// Add user id to variables
$m->id = 366;

// Add row to database after validation
$m->insert($m->variables());

// Get data
$m->get(1);

// Search in columns
$m->columns(['username','name','location']);

// Limit data
$m->limit(10,0)->search('a')->desc();

// Get all
$m->all();

// Count all
$m->count();
*/