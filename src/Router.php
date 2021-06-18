<?php
namespace Furo;

use Exception;
use Furo\Request;
use Furo\Entities\Route;

/**
 * Furo app router class
 */
class Router
{
	protected static $routes = [];
	protected static $currentRoute = '';
	protected static $errorClass;
	protected static $errorMethod;
	public static $httpMethods = ['GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'];

	private static $instance = null;

	public static function getInstance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}

	static function getRoutes() {
		return self::$routes;
	}

	static function getCurrentRoute() {
		return self::$currentRoute;
	}

	protected static function route($path, $class, $method = 'index', $middleware = [], $req = 'GET') {
		if(in_array($req, self::$httpMethods)) {
			self::$routes[$req][] = new Route($path, $class, $method, $middleware, $req);
		}
	}

	static function error($class, $method) {
		self::$errorClass = $class;
		self::$errorMethod = $method;
	}

	static function get($path, $class, $method, $middleware = []) {
		self::route($path, $class, $method, $middleware, 'GET');
	}

	static function post($path, $class, $method, $middleware = []) {
		self::route($path, $class, $method, $middleware, 'POST');
	}

	static function delete($path, $class, $method, $middleware = []) {
		self::route($path, $class, $method, $middleware, 'DELETE');
	}

	static function put($path, $class, $method, $middleware = []) {
		self::route($path, $class, $method, $middleware, 'PUT');
	}

	static function patch($path, $class, $method, $middleware = []) {
		self::route($path, $class, $method, $middleware, 'PATCH');
	}

	static function options($path, $class, $method, $middleware = []) {
		self::route($path, $class, $method, $middleware, 'OPTIONS');
	}

	static function redirect($route = '/', $redirect = '/index') {
		if(!empty($route) && !empty($redirect) && Request::url() == $route)
		{
			header("HTTP/1.1 301 Moved Permanently");
			header('Location: '.$redirect);
			exit;
		}
	}

	static function run() {
		$req = $_SERVER['REQUEST_METHOD'];
		$all = self::$routes[$req];
		foreach ($all as $route) {
			Request::$route = self::$currentRoute = $route->path;
			self::checkRoute($route->path, $route->class, $route->method, $route->middleware);
		}

		if(!empty(self::$errorClass) && !empty(self::$errorMethod)) {
			self::loadClassPath(self::$errorClass, self::$errorMethod);
		} else {
			throw new Exception("ERR_CREATE_ERROR_PAGE", 400);
		}
	}

	static function isIndexPage($url) {
		if(empty($url) || $url == '/' || $url == '/index.php'){
			return '/';
		}
		return $url;
	}

	static function checkRoute($route, $class, $method = 'Index', $middleware = [], $request = 'GET') {
		// replace {slug} from url
		$regex = preg_replace('/\{(.*?)\}/','[A-z0-9_.-]+',$route);
		$regex = str_replace("/", "\/", $regex);
		// curr url
		$url = self::isIndexPage(Request::url());
		// if url match route
		if(preg_match('/^'.$regex.'[\/]{0,1}$/', $url))
		{
			self::$currentRoute = $route; // Set route

			if(is_callable($class)){
				if(!empty($method)){
					self::middleware($middleware);
					echo $class($method); // Run func
				}else{
					self::middleware($middleware);
					echo $class(); // Run func
				}
				exit;
			}else{
				self::middleware($middleware);
				self::loadClassPath($class, $method); // Load class
			}
		}
	}

	static function middleware($arr) {
		foreach ($arr as $path) {
			$c = explode('::', $path);
			self::loadMiddleware($c[0], $c[1]);
		}
	}

	static function loadMiddleware($path, $method) {
		$path = self::clearClassPath($path);
		$o = new $path();
		if(method_exists($o, $method)){
			echo $o->$method();
		}else{
			throw new Exception('ERR_MIDDLEWARE');
		}
	}

	static function loadClassPath($path, $method) {
		// Load full class path (use namespace path)
		$path = self::clearClassPath($path);
		// Create object My\\Name\\Space\\Class
		$o = new $path();
		// Run method
		if(method_exists($o, $method)){
			echo $o->$method();
			exit;
		}else{
			throw new Exception('ERR_CONTROLLER');
		}
	}

	static function clearClassPath($path) {
		$path = str_replace('\\','/', rtrim($path, '/'));
		return str_replace('/','\\',ltrim($path,'\\'));
	}
}