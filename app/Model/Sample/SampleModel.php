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
// Validate
foreach ($_POST as $k => $v) {
	$m->$k = $v;
}
// Add row to database after validation
$m->insert($m->variables());
// Get data
$m->get(1);
// Limit data
$m->limit(10,0)->search('a')->desc();
// Get all
$m->all();
// Count all
$m->count();
*/