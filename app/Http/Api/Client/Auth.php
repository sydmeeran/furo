<?php
namespace App\Http\Api\Client;

use Exception;
use Furo\Db;
use Furo\Mail;
use Furo\Request;
use Furo\Response;
use Furo\Entities\Valid;
use Furo\Entities\Header;

/**
 * Authentication
 */
class Auth
{
	/**
	 * Login user
	 * curl -X POST -d 'pass=password&email=u1@woo.xx' -c /tmp/cookies.txt http://furo.xx/client/login -v
	 * Active session user (enable in routes.php )
	 * curl -X POST -d 'id=1' -b /tmp/cookies.txt http://furo.xx/client/active -v -i
	 *
	 * @return string Logged user json data
	 */
	function SignIn()
	{
		$ex = null;
		$msg = 'authenticated';

		try {
			// $id = (int) Request::get('id'); // $_GET['id']
			$email = Valid::email($_POST['email']);
			$pass = Valid::pass($_POST['pass']);

			$user = Db::query("SELECT * FROM user WHERE email = :e AND status != 'DELETED' ORDER BY id DESC LIMIT 1", [':e' => $email])->fetchObj();

			if($user->status == 'ONHOLD') {
				throw new Exception("ERR_ACTIVATION", 401);
			}
			if($user->pass == self::hash($pass)) {
				unset($user->pass); 		// Clear password
				$_SESSION['user'] = $user;  // Set user object
				self::userIp(); 			// Get user IP address
			} else {
				throw new Exception("ERR_CREDENTIALS", 401);
			}
		} catch (Exception $e) {
			$ex = $e;
			$msg = 'not_authenticated';
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'msg' => $msg
			]
		]);
	}

	/**
	 * Register user
	 * curl -X POST -d 'pass=password&email=u1@woo.xx' http://furo.xx/client/register
	 *
	 * @return string Json string
	 * @throws Exception
	 */
	function SignUp()
	{
		$ex = null;
		$msg = 'account_created';
		$uid = 0;
		$code = md5(uniqid());

		try {
			$email = Valid::email($_POST['email']);
			$pass = Valid::pass($_POST['pass']);
			self::accountExists($email);

			$uid = Db::query("INSERT INTO user(email,pass,code,username) VALUES(:e,:p,:c,UUID_SHORT())", [':e' => $email, ':p' => self::hash($pass), ':c' => $code])->lastInsertId();

			$html = Mail::theme('App\Entities\EmailTheme', 'Activation', ['{EMAIL}' => $email, '{CODE}' => $code]);
			Mail::send($email, 'Activation email', $html);

		} catch (Exception $e) {
			$ex = $e;
			$msg = 'account_not_created';

			if($uid > 0) {
				// If account has been created, after error email send delete account
				self::deleteAccount($uid);
			}
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'msg' => $msg
			]
		]);
	}

	/**
	 * Reset password
	 * curl -X POST -d 'email=u1@woo.xx' http://furo.xx/client/password
	 *
	 * @return string Json string
	 */
	function Password()
	{
		$ex = null;
		$msg = 'password_updated';

		try  {
			$email = Valid::email($_POST['email']);
			self::accountNotExists($email);

			$pass = uniqid();
			$cnt = Db::query("UPDATE user SET pass = :p WHERE email = :e", [':e' => $email, ':p' => md5($pass)])->rowCount();

			if($cnt > 0) {
				$html = Mail::theme('App\Entities\EmailTheme', 'Password', ['{EMAIL}' => $email, '{PASS}' => $pass]);
				Mail::send($email, 'New password', $html);
			} else {
				throw new Exception("ERR_PASSWORD_UPDATE", 400);
			}

		} catch (Exception $e) {
			$ex = $e;
			$msg = 'password_not_updated';
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'msg' => $msg
			]
		]);
	}

	/**
	 * Change password
	 * curl -X POST -d 'pass=password&pass1=password1&pass2=password1&email=u1@woo.xx' http://furo.xx/api/reset/password
	 *
	 * @return string Json string
	 */
	function ChangePassword()
	{
		$ex = null;
		$msg = 'password_updated';

		try  {
			$email = Valid::email($_POST['email']);
			$pass = Valid::pass($_POST['pass']);
			$pass1 = Valid::pass($_POST['pass1']);
			$pass2 = Valid::pass($_POST['pass2']);
			Valid::repeatPass($pass1, $pass2);
			self::accountNotExists($email);

			$cnt = Db::query("UPDATE user SET pass = :p WHERE email = :e AND pass = :cp", [':e' => $email, ':p' => md5($pass1), ':cp' => md5($pass)])->rowCount();

			if($cnt == 0) {
				throw new Exception("ERR_CURRENT_PASSWORD", 400);
			}

		} catch (Exception $e) {
			$ex = $e;
			$msg = 'password_not_updated';
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'msg' => $msg
			]
		]);
	}

	/**
	 * Activate user account
	 * curl -X GET http://furo.xx/client/activation/{code} -v
	 *
	 * @return string Json string
	 */
	function Activation()
	{
		$ex = null;
		$msg = 'account_activated';

		try {
			$code = Request::urlParam('code');
			self::codeNotExists($code);
			self::accountActiv($code);

			Db::query("UPDATE user SET status = 'ACTIVE' WHERE status = 'ONHOLD' AND code = :c", [':c' => $code])->rowCount();

		} catch (Exception $e) {
			$ex = $e;
			$msg = 'account_not_activated';
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'msg' => $msg
			]
		]);
	}

	/**
	 * Show logged user session data (dev only)
	 *
	 * @return string Loggoed user json data
	 */
	function Session()
	{
		// SESSION
		if(empty($_SESSION['user'])) {
			throw new Exception("ERR_UNAUTHORIZED", 401);
		}

		// RESPONSE
		return Response::json([
			'user' => $_SESSION['user']
		]);
	}

	/* Helper functions */

	// Delete user account
	protected static function deleteAccount($id)
	{
		Db::query("DELETE FROM user WHERE status = 'ONHOLD' AND id = :id", [':id' => (int) $id])->rowCount();
	}

	// Error if account exists
	protected static function accountExists($email)
	{
		Valid::email($email);

		$user = Db::query("SELECT id FROM user WHERE email = :e", [':e' => $email])->fetchObj();

		if($user->id > 0) {
			throw new Exception("ERR_ACCOUNT_EXISTS", 400);
		}
	}

	// Error if account does not exists
	protected static function accountNotExists($email)
	{
		Valid::email($email);

		$user = Db::query("SELECT id FROM user WHERE email = :e", [':e' => $email])->fetchObj();

		if($user->id == 0) {
			throw new Exception("ERR_ACCOUNT", 400);
		}
	}

	// If account alredy active
	protected static function accountActiv($code)
	{
		if(!empty($code)) {
			$user = Db::query("SELECT id FROM user WHERE code = :c AND status = 'ACTIVE'", [':c' => $code])->fetchObj();

			if($user->id > 0) {
				throw new Exception("ERR_ACCOUNT_ACTIVATED", 400);
			}
		} else {
			throw new Exception("ERR_CODE", 400);
		}
	}

	// if code does not exists
	protected static function codeNotExists($code)
	{
		if(!empty($code)) {
			$user = Db::query("SELECT id FROM user WHERE code = :c", [':c' => $code])->fetchObj();

			if($user->id == 0) {
				throw new Exception("ERR_CODE", 400);
			}
		} else {
			throw new Exception("ERR_CODE", 400);
		}
	}

	// password hash
	protected static function hash($str)
	{
		return md5($str);
	}

	protected static function userIp()
	{
		$_SESSION['user_ip'][] = $_SERVER['REMOTE_ADDR'];
		if(!empty(Header::getHeader('X-Real-Ip'))) {
			$_SESSION['user_ip'][] = Header::getHeader('X-Real-Ip');
		}
	}
}