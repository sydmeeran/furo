<?php
namespace Furo;

use Furo\Entities\Header;

class Request
{
	static $route = '';
	static $url = '';
	static $urlQuery = '';
	static $variables = [];

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

	static function uri()
	{
		return $_SERVER['REQUEST_URI'];
	}

	static function url()
	{
		return self::$url = '/'.self::trimUrl(parse_url(self::uri(), PHP_URL_PATH));
	}

	static function trimUrl($url) {
		$url = str_replace('..','__',$url);
		return ltrim(rtrim(trim($url), '/'), '/');
	}

	static function urlQuery()
	{
		parse_str(parse_url(self::uri(), PHP_URL_QUERY), self::$urlQuery);
		return self::$urlQuery;
	}

	static function urlQueryParam($name)
	{
		parse_str(parse_url(self::uri(), PHP_URL_QUERY), self::$urlQuery);
		return self::$urlQuery[$name];
	}

	static function urlParam($name = 'id') {
		if(!empty(self::$route)){
			$u = explode('/', Request::url());
			$r = explode('/', self::$route);
			foreach ($r as $k => $v) {
				if($v == '{'.$name.'}'){
					return $u[$k];
				}
			}
		}
		return '';
	}

	static function setEnv($name, $val) {
		self::$variables[strtolower($name)] = $val;
	}

	static function getEnv($name) {
		return self::$variables[strtolower($name)];
	}

	static function getVars() {
		return self::$variables;
	}

	static function get($name) {
		return $_GET[$name];
	}

	static function post($name) {
		return $_POST[$name];
	}

	static function bearerToken()
	{
		return Header::bearerToken();
	}
}
