<?php
namespace Furo\Entities;

use Exception;

class SetGet
{
	public function __get($key)
	{
		if (property_exists($this, $key)) {
			return $this->$key;
		}
	}

	public function __set($key, $value)
	{
		if (property_exists($this, $key)) {
			$this->$key = $value;
		}
	}

	public function __call($name, $args)
    {
		throw new Exception("ERR_METHOD", 422);
    }

    public static function __callStatic($name, $args)
    {
		throw new Exception("ERR_METHOD", 422);
	}
}