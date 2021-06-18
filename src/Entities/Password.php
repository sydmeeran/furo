<?php
namespace Furo\Entities;

use Exception;

class Password
{
	static $length = 8;

	static function length($val = 8)
	{
		self::$length = $val;
		return new self();
	}

	static function valid($pass, $secure = 0, $special = 0)
	{
		if($secure == 1) {
			self::isPassSecure($pass, self::$length, $special);
		} else {
			self::isPass($pass, self::$length);
		}
	}

	protected static function isPass($str, $length = 8)
	{
		if(strlen($str) < $length || empty($str)) {
			throw new Exception("ERR_PASS_LENGTH", 402);
		}
	}

	protected static function isPassSecure($str, $length = 8, $special = 0)
	{
		if(strlen($str) < $length || empty($str)) {
			throw new Exception("ERR_PASS_LENGTH", 402);
		}

		if(!preg_match('/[A-Z]/', $str)) {
			throw new Exception("ERR_PASS_BIG_LETTER", 402);
		}

		if(!preg_match('/[0-9]/', $str)) {
			throw new Exception("ERR_PASS_NUMBER", 402);
		}

		if(!preg_match('/[a-z]/', $str)) {
			throw new Exception("ERR_PASS_SMALL_LETTER", 402);
		}

		if($special == 1) {
			if(!preg_match('/[^a-zA-Z\d]/', $str)) {
				throw new Exception("ERR_PASS_SPECIAL_LETTER", 402);
			}
		}
	}
}