<?php
namespace Furo\Traits;

use Exception;
use Furo\Db;

/**
 * Create and validate object from $_POST request or PDO fetch class
 *
 * use Furo\Traits\ModelVariable;
 * use Furo\Traits\ModelDatabase;
 *
 * $_POST['email'] = 'sample@email.com';
 *
 * class SampleModel
 * {
 * 		trait ModelVariable; // Validation
 * 		trait ModelDatabase; // Model methods (optional)
 *
 * 		protected $email;
 *
 * 		protected function email($str) {
 * 			// Validate email here, on error throw exception
 * 			// throw new Exception('ERR_EMAIL', 400);
 * 		}
 *
 * 		function __construct()
 *		{
 *			$this->table('user');
 *			$this->columns(['name','username','email', 'price']);
 *		}
 *
 *		function add(array $arr)
 *		{
 *			// Override this method
 *			throw new Exception("ERR_ADD_METHOD", 400);
 *			return 0;
 *		}
 * }
 *
 * $o = new SampleModel();
 *
 * // Validate
 * foreach($_POST as $property => $value) {
 * 		$o->$property = $value;
 * }
 *
 * // Properties
 * echo $o->email;
 * echo $o->email();
 *
 * // Db
 * $o->columns(['username','name'])->table('addon')->limit(12,1);
 * $o->where('username', 'ben', '!=')->search('adm');
 * $o->range_out('price',26.0,96.0);
 * print_r($o->all());
 * $user = $o->get(1);
 *
 * // $o->desc();
 * // $o->update(['name' => 'ADMIN'], 321);
 * // $o->select("user.*, user_token.token");
 * // $o->join("LEFT JOIN user_token ON user.id = user_token.user_id");
 */
trait ModelDatabase
{
	protected $id = 'id';
	protected $table = 'user';
	protected $limit = 0;
	protected $offset = 0;
	protected $search = '';
	protected $params = [];
	protected $sql_limit = '';
	protected $sql_search = '';
	protected $sql_order = '';
	protected $sql_join = '';
	protected $sql_columns = '*';
	protected $sql_search_value = '';
	protected $columns = ['username','name','location','mobile','about','www'];

	/**
	 * Id
	 *
	 * @param string $column Primary key column
	 * @return object
	 */
	function id($column)
	{
		$this->id = (string) $column;
		return $this;
	}

	/**
	 * Set table
	 *
	 * @param string $str Table name
	 * @return object
	 */
	function table($str)
	{
		$this->table = (string) $str;
		return $this;
	}

	/**
	 * Allow columns
	 *
	 * @param array $arr Update, search allowed columns
	 * @return object
	 */
	function columns($arr)
	{
		$this->columns = (array) $arr;
		return $this;
	}

	/**
	 * Limit
	 *
	 * @param integer $limit Limit records
	 * @param integer $offset Records offset
	 * @return object
	 */
	function limit($limit, $offset = 0)
	{
		$limit = (int) $limit;
		$offset = (int) $offset;

		if($limit > 0) {
			$this->sql_limit = "LIMIT $limit OFFSET $offset ";
		}
		return $this;
	}

	/**
	 * Sorting DESC
	 *
	 * @param string $column Sort by column
	 * @return object
	 */
	function desc($column = '')
	{
		if(empty($column)) {
			$column = $this->id;
		}
		$this->sql_order = " ORDER BY $column DESC";
		return $this;
	}

	/**
	 * Regex search string
	 *
	 * @param string $str Search strings
	 * @return object
	 */
	function search($str)
	{
		if(!empty($str)) {
			$add = "WHERE";
			if(!empty($this->sql_search)) { $add = "AND"; }

			$this->search = str_replace(" ", "|", $str);
			if(!empty($this->search)) {
				$this->params[':regexp'] = trim($this->search," %");
				$this->sql_search .= "$add CONCAT_WS(' ',".implode(",",$this->columns).") REGEXP :regexp ";
			}
		}
		return $this;
	}

	/**
	 * Where search
	 *
	 * @param string $col Column name
	 * @param string $val Value
	 * @param string $operator
	 * @return object
	 */
	function where($col, $val, $operator = '=')
	{
		$id = uniqid();
		$add = "WHERE ";
		$this->params[':val_'.$id] = $val;
		if(!empty($this->sql_search)) { $add = "AND "; }
		$this->sql_search .= "$add $col $operator :val_$id ";
		return $this;
	}

	/**
	 * Range search
	 * x >= from and x <= to
	 *
	 * @param string $col Columna
	 * @param integer $from From value
	 * @param integer $to To value
	 * @return object
	 */
	function range_in($col = 'price', $from = 0, $to = 0)
	{
		$id = uniqid();
		$add = "WHERE";
		$this->params[':from_'.$id] = $from;
		$this->params[':to_'.$id] = $to;
		if(!empty($this->sql_search)) { $add = "AND"; }
		$this->sql_search .= "$add $col >= :from_$id AND $col <= :to_$id ";
		return $this;
	}

	/**
	 * Range search
	 * x <= from and x >= to
	 *
	 * @param string $col Columna
	 * @param integer $from From value
	 * @param integer $to To value
	 * @return object
	 */
	function range_out($col = 'price', $from = 0, $to = 0)
	{
		$id = uniqid();
		$add = "WHERE";
		$this->params[':from_'.$id] = $from;
		$this->params[':to_'.$id] = $to;
		if(!empty($this->sql_search)) { $add = "AND"; }
		$this->sql_search .= "$add ($col <= :from_$id OR $col >= :to_$id) ";
		return $this;
	}

	/**
	 * Join sql, use join() with select() for columns sustomization
	 *
	 * @param string $sql Mysql query sql join part:
	 * LEFT JOIN user_token ON user.id = user_token.user_id
	 * @return object
	 */
	function join($sql)
	{
		if(!empty($sql)) {
			$this->sql_join = (string) $sql;
		}
		return $this;
	}

	/**
	 * Select rows list
	 *
	 * @param string $columns  Select a comma-separated list of query columns:
	 * user.*, user_token.token
	 * @return object
	 */
	function select($columns = '*')
	{
		if(!empty($columns)) {
			$this->sql_columns = (string) $columns;
		}
		return $this;
	}

	/**
	 * Get record with id
	 *
	 * @param int $id Row id
	 * @return object Table row object
	 */
	function get($id)
	{
		return Db::query("SELECT $this->sql_columns FROM $this->table $this->sql_join WHERE $this->id = :id", [':id' => $id])->fetchObj();
	}

	/**
	 * Get record with column
	 *
	 * @param string $name Column name
	 * @param string $value Column value
	 * @return object Table row object
	 */
	function getc($name, $value)
	{
		if(in_array($name, $this->columns) && !empty($value)) {
			return Db::query("SELECT $this->sql_columns FROM $this->table $this->sql_join WHERE $name = :v", [':v' => $value])->fetchObj();
		}
	}

	/**
	 * Get records
	 *
	 * @param int $limit Rows on page
	 * @param int $offset Rows offset
	 * @param int $search Search word
	 * @return array Array with objects
	 */
	function all()
	{
		return Db::query("SELECT $this->sql_columns FROM $this->table $this->sql_join $this->sql_search $this->sql_order $this->sql_limit", $this->params)->fetchAllObj();
	}

	/**
	 * Count all records (always after search method)
	 *
	 * @param int $search Search word
	 * @return int Counted rows
	 */
	function count()
	{
		$o = Db::query("SELECT COUNT(*) as cnt FROM $this->table $this->sql_join $this->sql_search", $this->params)->fetchObj();
		if($o->cnt >= 0) {
			return $o->cnt;
		}
		return 0;
	}

	/**
	 * Save or update record in database
	 *
	 * @param array $arr Self class object
	 * @return void
	 */
	function update(array $arr, int $uid)
	{
		if($uid > 0) {
			foreach ($arr as $col => $v) {
				if(in_array($col, $this->columns)) {
					Db::query("UPDATE $this->table SET $col = :v WHERE id = :id", [':v' => $v, ':id' => $uid])->rowCount();
				}
			}
		}
	}

	/**
	 * Delete record
	 *
	 * @param array $arr Post array
	 * @return int Deleted rows
	 */
	function delete(int $id)
	{
		return Db::query("DELETE FROM $this->table WHERE $this->id = :id", [':id' => $id])->rowCount();
	}

	/**
	 * Delete record
	 *
	 * @param string $name Column name
	 * @param string $value Column value
	 * @return int Deleted rows
	 */
	function deletec($name, $value)
	{
		if(in_array($name, $this->columns) && !empty($value)) {
			return Db::query("DELETE FROM $this->table WHERE $name = :v", [':v' => $value])->rowCount();
		}
		return 0;
	}

	/**
	 * Sql query fetch all records
	 *
	 * @param int $sql Mysql query string
	 * @return array Array with objects
	 */
	function sql($sql, $params)
	{
		return Db::query($sql, $params)->fetchAllObj();
	}

	/**
	 * Add record to table
	 *
	 * @param array $arr Post array
	 * @return int Last inserted id
	 */
	function add(array $arr)
	{
		// Override this method
		throw new Exception("OVERRIDE_MODEL_ADD_METHOD", 402);
		return 0;
	}
}