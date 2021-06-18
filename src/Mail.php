<?php
namespace Furo;

use DOMDocument;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Mail
{
	// From email
	static $FROM_EMAIL = 'no-reply@local.host';
	static $FROM_USER = 'Welcome';
	static $USER = '';
	static $PASS = ''; // Smtp (default localhost without pass)
	static $HOST = '127.0.0.1';
	static $PORT = 25; // 25, 587, 465
	static $TLS = ''; // tls or ssl - enable tls/ssl connection
	static $AUTH = false; // true - enable authentication
	static $DEBUG = 0; // Or 1
	static $SELFSIGNED = true;
	static $UTF8 = false;

	private static $instance = null;

	public static function getInstance(): self
	{
		// static $instance;
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}

	static function from($email,$name = '')
	{
		self::isValidEmail($email);
		self::$FROM_EMAIL = $email;
		self::$FROM_USER = $name;
		return self::getInstance();
	}

	static function user($email)
	{
		self::$USER = $email;
		return self::getInstance();
	}

	static function pass($pass)
	{
		self::$PASS = $pass;
		return self::getInstance();
	}

	static function host($host)
	{
		self::$HOST = $host;
		return self::getInstance();
	}

	static function port($nr)
	{
		self::$PORT = $nr;
		return self::getInstance();
	}

	static function tls()
	{
		self::$TLS = 'tls';
		return self::getInstance();
	}

	static function ssl()
	{
		self::$TLS = 'ssl';
		return self::getInstance();
	}

	static function auth()
	{
		self::$AUTH = true;
		return self::getInstance();
	}

	static function debug()
	{
		self::$DEBUG = true;
		return self::getInstance();
	}

	static function allowSelfSigned()
	{
		self::$SELFSIGNED = true;
		return self::getInstance();
	}

	static function utf8()
	{
		self::$UTF8 = true;
		return self::getInstance();
	}

	static function isValidEmail($email): void
	{
		if(preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $email) != 1) {
			throw new Exception("ERR_EMAIL");
		}
	}

	static function send($email, $subject, $html, $files = [], $inline = [])
	{
		return self::smtp($email, $subject, $html, $files, $inline, self::$FROM_EMAIL, self::$FROM_USER, self::$USER, self::$PASS, self::$HOST, self::$TLS, self::$AUTH, self::$PORT, self::$DEBUG);
	}

	protected static function smtp($email, $subject, $html, $files, $inline, $from_email, $from_user, $smtpUser, $smtpPass, $smtpHost, $smtpTls = false, $smtpAuth = false, $smtpPort = 25, $smtpDebug = 0)
	{
		try {
			$m = new PHPMailer(true); // Passing `true` enables exceptions
			$m->SMTPDebug = (int) $smtpDebug;
			$m->isSMTP();
			$m->Host = $smtpHost;
			$m->Port = $smtpPort;
			$m->SMTPAuth = $smtpAuth;
			$m->Username = $smtpUser;
			$m->Password = $smtpPass;
			$m->XMailer = ' ';
			if(self::$UTF8 == true) {
				$m->CharSet = 'UTF-8';
				$m->Encoding = 'base64';
			}
			if(self::$SELFSIGNED == true) {
				$m->SMTPOptions = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false,'allow_self_signed' => true));
			}
			// Tls
			if($smtpTls == 'tls') {
				$m->SMTPSecure = 'tls';
			}
			// Ssl
			if($smtpTls == 'ssl') {
				$m->SMTPSecure = 'ssl';
			}
			$m->setFrom($from_email, $from_user);
			$m->addReplyTo($from_email, $from_user);
			$m->addAddress($email);
			$m->Subject = $subject;
			$m->isHTML(true); // Set email format to HTML
			$m->Body = $html;
			$m->AltBody = 'Change to html view.';
			// Add files from array
			foreach ($files as $path) {
				if(file_exists($path)) { $m->addAttachment($path); }
			}
			// Add inline images <img src="cid:img-name">
			foreach ($inline as $cid => $path) {
				if(file_exists($path)) { $m->addEmbeddedImage($path, $cid, basename($path)); }
			}
			// Send
			$m->send();
		} catch (PHPMailerException $e) {
			throw $e;
		}
	}

	static function themeHtml($path, $data)
	{
		if(file_exists($path)) {
			$txt = file_get_contents($path);
			return self::closeTags(self::replaceTags($txt, $data));
		} else {
			throw new Exception('ERR_EMAIL_THEME');
		}
	}

	static function theme($class = 'App\Entities\EmailTheme', $method = 'Html', $data = []) {
		$cl = self::clearClassPath($class);
		$o = new $cl();
		if(method_exists($o, $method)){
			$txt = $o->$method();
			return self::closeTags(self::replaceTags($txt, $data));
		}else{
			throw new Exception('ERR_EMAIL_THEME');
		}
	}

	static function clearClassPath($path) {
		$x = str_replace('\\','/', rtrim($path, '/'));
		return str_replace('/','\\',ltrim($path,'\\'));
	}

	static function replaceTags($html, $arr)
	{
		$h = $html;
		if(empty($arr['{DOMAIN}'])) {
			$arr['{DOMAIN}'] = $_SERVER['HTTP_HOST'];
		}
		foreach ($arr as $k => $v) {
			$h = str_replace($k, $v, $h);
		}
		return $h;
	}

	static function closeTags($html = '<h1>He <a href=""> llo </h1>')
	{
		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->encoding='UTF-8';
		$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		return $doc->saveHTML();
	}

	static function toUtf8($html)
	{
		return mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
	}
}
?>