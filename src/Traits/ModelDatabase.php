<?php
namespace Furo\Traits;

use Furo\Db;

/**
 * Db model class
 *
 * use Furo\Traits\ModelDatabase;

 * class SampleModel
 * {
 * 		trait ModelDatabase; // Model methods (optional)
 * 		function __construct()
 *		{
 *			$this->table('user');
 *			$this->columns(['name','username','email', 'price']);
 *		}
 *		function add(array $arr)
 *		{
 *			// Override this method
 *			throw new Exception("ERR_ADD_METHOD", 400);
 *			return 0;
 *		}
 * }
 *
 * // Get data
 * $o = new SampleModel();
 * $user = $o->get(1);
 * $list = $o->all();
 *
 * // Examples
 * $o->columns(['username','name'])->table('addon')->limit(25,0);
 * $o->where('username', 'ben', '!=')->search('admin');
 * $o->range_out('price',26.0,96.0);
 *
 * $o->desc();
 * $o->update(['name' => 'ADMIN'], $user->id);
 * $o->select("user.*, user_token.token");
 * $o->join("LEFT JOIN user_token ON user.id = user_token.user_id");
 */
trait ModelDatabase
{
	protected $id = 'id';
	protected $table = 'user';
	protected $limit = 0;
	protected $offset = 0;
	protected $search = '';
	protected $params = [];
	protected $params_insert = [];
	protected $sql_limit = '';
	protected $sql_search = '';
	protected $sql_order = '';
	protected $sql_join = '';
	protected $sql_columns = '*';
	protected $sql_search_value = '';
	protected $sql_insert = '';
	protected $columns = ['username','name','location','mobile','about','www'];

	/**
	 * Id
	 *
	 * @param string $column Primary key column
	 * @return object
	 */
	final function id($column)
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
	final function table($str)
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
	final function columns($arr)
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
	final function limit($limit, $offset = 0)
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
	final function desc($column = '')
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
	final function search($str)
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
	final function where($col, $val, $operator = '=')
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
	final function range_in($col = 'price', $from = 0, $to = 0)
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
	final function range_out($col = 'price', $from = 0, $to = 0)
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
	final function join($sql)
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
	final function select($columns = '*')
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
	final function get($id)
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
	final function getc($name, $value)
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
	final function all()
	{
		return Db::query("SELECT $this->sql_columns FROM $this->table $this->sql_join $this->sql_search $this->sql_order $this->sql_limit", $this->params)->fetchAllObj();
	}

	/**
	 * Count all records (always after search method)
	 *
	 * @param int $search Search word
	 * @return int Counted rows
	 */
	final function count()
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
	final function update(array $arr, int $uid)
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
	final function delete(int $id)
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
	final function deletec($name, $value)
	{
		if(in_array($name, $this->columns) && !empty($value)) {
			return Db::query("DELETE FROM $this->table WHERE $name = :v", [':v' => $value])->rowCount();
		}
		return 0;
	}

	/**
	 * Insert row into table
	 *
	 * @param array $arr Array with pairs (key,value)
	 * @return int Last inserted id
	 */
	function insert(array $arr)
	{
		$sql = '';
		$sql_param = '';
		foreach ($arr as $k => $v) {
			$sql .= $k.',';
			$sql_param .= ':'.$k.',';
			$this->params_insert[':'.$k] = $v;
		}
		$sql = trim($sql, ',');
		$sql_param = trim($sql_param, ',');
		$this->sql_insert = 'INSERT INTO '.$this->table .'('.$sql.') VALUES('.$sql_param.')';
		return Db::query($this->sql_insert,$this->params_insert)->lastInsertId();
	}
}