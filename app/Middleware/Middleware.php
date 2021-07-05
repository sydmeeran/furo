<?php
namespace App\Middleware;

use Exception;
use Furo\Db;
use Furo\Request;
use Furo\Entities\Header;

class Middleware
{
	// Session logged user
	static function IsLogged()
	{
		$user = $_SESSION['user'];
		$user_ip = $_SESSION['user_ip'];

		if(!in_array($_SERVER['REMOTE_ADDR'], $user_ip)) {
			throw new Exception('ERR_AUTH_IP', 401);
		}

		if(empty($user) || $user->id <= 0 || empty($user_ip)) {
			throw new Exception('ERR_NOT_AUTHENTICATED', 401);
		}

		if(!in_array($user->status, ['ACTIVE'])) {
			throw new Exception('ERR_NOT_ACTIVATED', 401);
		}

		Request::setEnv('user', $user);
	}

	// Session user
	static function IsLoggedStuff($roles = ['admin','worker'])
	{
		$user = $_SESSION['user'];
		$user_ip = $_SESSION['user_ip'];

		if(!in_array($_SERVER['REMOTE_ADDR'], $user_ip)) {
			throw new Exception('ERR_AUTH_IP', 401);
		}

		if(empty($user) || $user->id <= 0 || empty($user_ip)) {
			throw new Exception('ERR_NOT_AUTHENTICATED', 401);
		}

		if(!in_array($user->role, $roles)) {
			throw new Exception('ERR_AUTH_ROLE', 401);
		}

		if(!in_array($user->status, ['ACTIVE'])) {
			throw new Exception('ERR_NOT_ACTIVATED', 401);
		}

		Request::setEnv('user', $user);
	}

	// Header authorization token
	static function AuthToken()
	{
		$token = Header::bearerToken();

		if(empty($token)) {
			throw new Exception('ERR_BEARER_TOKEN');
		}

		Request::setEnv('token', $token);
	}

	// Log to file
	static function Log()
	{
		// Save in virtualhost error_log file
		error_log("Url: " . Request::url() . ' Token: ' . Request::getEnv('token') . ' IP: ' . $_SERVER['REMOTE_ADDR'] , 0);
	}
}