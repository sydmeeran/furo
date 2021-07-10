<?php
namespace Furo\Entities;

use Exception;
use DateTime;
use Furo\Entities\Password;

/**
 * Validate class
 */
class Valid
{
	/**
	 * Uuid - Test unique id string
	 *
	 * @param string $str UUID String
	 * @return mixed Valid string or value
	 */
	static function uuid(string $str)
	{
		if(preg_match('/^(\w+\-){4}\w+$/', $str) != 1)
		{
			throw new Exception("ERR_UUID", 400);
		}
		return $str;
	}

	/**
	 * Alias - Test alias
	 *
	 * @param string $str
	 * @return mixed Valid string or value
	 */
	static function alias(string $str)
	{
		if(preg_match('/^[a-zA-Z]{1}[a-zA-Z0-9\.]{2,30}[a-zA-Z0-9]{1}$/', $str) != 1)
		{
			throw new Exception("ERR_ALIAS", 400);
		}
		return $str;
	}

	/**
	 * Email - Validate email
	 *
	 * @param string $val
	 * @param integer $size
	 * @return mixed Valid string or value
	 */
	static function email(string $val, int $size = 190)
	{
		if(preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $val) != 1 || strlen($val) > $size)
		{
			throw new Exception("ERR_EMAIL", 400);
		}
		return $val;
	}

	/**
	 * Pass - Validate password
	 *
	 * @param string $str Password string
	 * @param integer $length Pass min length
	 * @param integer $secure Big, small letters and numbers
	 * @param integer $special Special character
	 * @return mixed Valid string or value
	 */
	static function pass(string $str, int $length = 8, int $secure = 0, int $special = 0)
	{
		Password::length($length)::valid($str, $secure, $special);
		return $str;
	}

	static function repeatPass($pass1, $pass2)
	{
		if($pass1 != $pass2) {
			throw new Exception("ERR_PASS", 400);
		}
		return $pass1;
	}

	/**
	 * Date - Validate date string in format
	 *
	 * @param string $date Date string
	 * @param string $format Format string
	 * @return mixed Valid string or value
	 */
	static function date(string $date, string $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		if(!($d && $d->format($format) == $date))
		{
			throw new Exception("ERR_TIMESTAMP", 400);
		}
		return $date;
	}

	/**
	 * Empty - Test is string empty
	 *
	 * @param string $str Test string
	 * @return mixed Valid string or value
	 */
	static function empty($str)
	{
		if(empty($str))
		{
			throw new Exception("ERR_EMPTY_VALUE", 400);
		}
		return $str;
	}

	/**
	 * InArray - Test is value exists in array
	 *
	 * @param string $val String value
	 * @param array $arr Array with values
	 * @return mixed Valid string or value
	 */
	static function inArray(string $val, array $arr)
	{
		if(!in_array($val, $arr))
		{
			throw new Exception("ERR_ARRAY", 400);
		}

		return $val;
	}

	/**
	 * Slug
	 *
	 * @param string $str String value
	 * @return mixed Valid string or value
	 */
	static function slug(string $str)
	{
		if(preg_match('/^[a-zA-Z]{1}[a-zA-Z0-9_-]{2,}[a-zA-Z0-9]{1}$/', $str) != 1)
		{
			throw new Exception("ERR_SLUG", 400);
		}
		return $str;
	}

	/**
	 * Unsigned - If value < 0 throw error
	 *
	 * @param string $str Test value
	 * @return mixed Valid string or value
	 */
	static function unsigned(int $val)
	{
		if($val < 0)
		{
			throw new Exception("ERR_UNSIGNED_VALUE", 400);
		}
		return $val;
	}

	/**
	 * Id test
	 *
	 * @param integer $val Id value
	 * @return mixed Valid string or value
	 */
	static function id(int $val)
	{
		if($val <= 0)
		{
			throw new Exception("ERR_ID", 400);
		}
		return $val;
	}

	/**
	 * Decimal - If value not decimal throw error
	 *
	 * @param string $str Test value
	 * @return mixed Valid string or value
	 */
	static function decimal(string $val)
	{
		if(preg_match('/^[0-9]+\.[0-9]{1,2}$/', $val) != 1)
		{
			throw new Exception("ERR_DECIMAL_VALUE", 400);
		}
		return $val;
	}

	static function color(string $hex)
	{
		if(preg_match('/^#[a-zA-Z0-9]{6}$/', $hex) != 1)
		{
			throw new Exception("ERR_COLOR_HEX_VALUE", 400);
		}

		return $hex;
	}
}