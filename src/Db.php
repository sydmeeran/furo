<?php
declare(strict_types=1);
namespace Furo;

use PDO;
use Exception;
use Redis;

class Db
{
	public static $pdo = null;
	protected static $stm = null;
	// Mysql
	protected static $MYSQL_DBNAME = 'app';
	protected static $MYSQL_HOST = 'localhost';
	protected static $MYSQL_USER = 'root';
	protected static $MYSQL_PASS = 'toor';
	protected static $MYSQL_PORT = 3306;
	// Redis
	protected static $REDIS_PASS = '';
	protected static $REDIS_HOST = 'localhost';
	protected static $REDIS_PORT = 6379;
	protected static $REDIS_TTL = 600;

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

	final static function host($host)
	{
		self::$MYSQL_HOST = $host;
		return self::getInstance();
	}

	final static function port($port)
	{
		self::$MYSQL_PORT = (int) $port;
		return self::getInstance();
	}

	final static function database($name)
	{
		self::$MYSQL_DBNAME = $name;
		return self::getInstance();
	}

	final static function user($name)
	{
		self::$MYSQL_USER = $name;
		return self::getInstance();
	}

	final static function pass($pass)
	{
		self::$MYSQL_PASS = $pass;
		return self::getInstance();
	}

	final static function redisTTL($sec)
	{
		self::$REDIS_TTL = (int) $sec;
		return self::getInstance();
	}

	final static function redisHost($host)
	{
		self::$REDIS_HOST = $host;
		return self::getInstance();
	}

	final static function redisPort($port)
	{
		self::$REDIS_PORT = (int) $port;
		return self::getInstance();
	}

	final static function redisPass($pass)
	{
		self::$REDIS_PASS = $pass;
		return self::getInstance();
	}

	/**
	 * PDO Connection
	 *
	 * Ssl
	 * ALTER USER 'ssl'@'%' REQUIRE SSL
	 * @return void
	 */
	final static function conn(){
		try
		{
			// pdo
			$con = new PDO('mysql:host='.self::$MYSQL_HOST.';port='.self::$MYSQL_PORT.';dbname='.self::$MYSQL_DBNAME.';charset=utf8mb4', self::$MYSQL_USER, self::$MYSQL_PASS);
			// show warning text
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			// throw error exception
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// default fetch mode
			$con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // PDO::FETCH_ASSOC
			// don't colose connecion on script end
			$con->setAttribute(PDO::ATTR_PERSISTENT, true);
			// set utf for connection utf8_general_ci or utf8_unicode_ci
			$con->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
			// prepared statements, don't cache query with prepared statments
			$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			// Multiple statments
			$con->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS,false);
			// auto commit
			// $con->setAttribute(PDO::ATTR_AUTOCOMMIT,flase);
			// buffered querry default
			// $con->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
			// Ssl
			// $con->setAttribute(PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT,false);
			// $con->setAttribute(PDO::MYSQL_ATTR_SSL_CA,'/etc/mysql/certs/ca-cert.pem');
			// $con->setAttribute(PDO::MYSQL_ATTR_SSL_CA,'/etc/mysql/certs/server-cert.pem');
			// Optional
			// $con->setAttribute(PDO::MYSQL_ATTR_SSL_KEY,'/etc/mysql/ssl/client-key.pem');
			// $con->setAttribute(PDO::MYSQL_ATTR_SSL_CERT,'/etc/mysql/ssl/client-cert.pem');
			return $con;
		}
		catch(Exception $e)
		{
			throw $e;
			// echo 'ERR_CONN: ' . $e->getMessage ();
			// print_r($e->errorInfo());
			// return null;
		}
	}

	/**
	 * Mysql query
	 *
	 * $row = Db::query("SELECT * FROM user WHERE id = :id", [':id' => 123456])->FetchObj();
	 * $rows = Db::query("SELECT * FROM user WHERE id > :id", [':id' => 0])->FetchAll();
	 *
	 * @param string $sql Mysql query
	 * @param array $arr Array with params for secure sql injection
	 * @return object Return self object
	 */
	static function query($sql, $arr = array())
	{
		self::$pdo = self::conn();
		self::$stm = self::$pdo->prepare($sql);
		self::$stm->execute($arr);

		return self::getInstance();
	}

	/**
	 * Fetch table row
	 *
	 * @return array Table row array
	 */
	function fetch()
	{
		return self::$stm->fetch();
	}

	/**
	 * Fetch table row
	 *
	 * @return object Table row array
	 */
	function fetchObj($class = '')
	{
		if(!empty($class)) {
			return self::$stm->fetch(PDO::FETCH_CLASS, $class);
		}
		return self::$stm->fetch(PDO::FETCH_OBJ);
	}

	/**
	 * Fetch table rows
	 *
	 * @return array Table rows array
	 */
	function fetchAll()
	{
		return self::$stm->fetchAll();
	}

	/**
	 * Fetch table rows
	 *
	 * @return array Table rows array
	 */
	function fetchAllObj($class = '')
	{
		if(!empty($class)) {
			return self::$stm->fetchAll(PDO::FETCH_CLASS, $class);
		}
		return self::$stm->fetchAll(PDO::FETCH_OBJ);
	}

	/**
	 * Count table rows
	 *
	 * @return int Number
	 */
	function rowCount()
	{
		return self::$stm->rowCount();
	}

	/**
	 * Last insert record id
	 *
	 * @return int Number
	 */
	function lastInsertId()
	{
		return self::$pdo->lastInsertId();
	}

	/**
	 * Get data from db or redis cache if exists in it
	 *
	 * @param string $sql Mysql Query
	 * @param array $arr pdo params array with [':val' => 'string']
	 * @return mixed Array with objects from cache or db
	 */
	static function queryCache(string $sql, array $arr, $fetchSingleRow = false, $class = '')
	{
		$re = new Redis();
		$re->connect(self::$REDIS_HOST, self::$REDIS_PORT);
		if(!empty(self::$REDIS_PASS)) {
			$re->auth(self::$REDIS_PASS);
		}

		$key = md5($sql.serialize($arr));
		if($re->exists($key)) {
			return unserialize($re->get($key));
		}

		if($fetchSingleRow) {
			$rows =  self::query($sql,$arr)->fetchObj($class);
		} else {
			$rows =  self::query($sql,$arr)->fetchAllObj($class);
		}

		$re->set($key, serialize($rows), self::$REDIS_TTL);

		return $rows;
	}
}
