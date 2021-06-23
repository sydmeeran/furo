<?php
namespace App\Middleware;

use Exception;
use Furo\Db;
use Furo\Request;
use Furo\Entities\Header;

class Middleware
{
	// Session user
	static function IsLogged()
	{
		$user = $_SESSION['user'];

		if(empty($user)) {
			throw new Exception('ERR_SESS_USER', 401);
		}

		if(!in_array($user->role,['user'])) {
			throw new Exception('ERR_SESS_USER_ROLE', 401);
		}

		if(!in_array($user->status, ['ACTIVE'])) {
			throw new Exception('ERR_SESS_USER_STATUS', 401);
		}

		Request::setEnv('user', $user);
	}

	// Session user
	static function IsLoggedStuff()
	{
		$user = $_SESSION['user'];

		if(empty($user)) {
			throw new Exception('ERR_USER', 401);
		}

		if(!in_array($user->role, ['admin','worker'])) {
			throw new Exception('ERR_USER_ROLE', 401);
		}

		if(!in_array($user->status, ['ACTIVE'])) {
			throw new Exception('ERR_USER_STATUS', 401);
		}

		Request::setEnv('user', $user);
	}

	static function SetLoggedUser()
	{
		Request::setEnv('logged_user', (array) $_SESSION['user']);
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