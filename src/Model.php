<?php
namespace Furo;

use Exception;
use Furo\Db;

class Model
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
	protected $columns = ['username','name','location','mobile','about','www'];

	/**
	 * Id
	 *
	 * @param string $column Primary key column
	 * @return void
	 */
	function id($column)
	{
		$this->id = (string) $column;
	}

	/**
	 * Set table
	 *
	 * @param string $str Table name
	 * @return void
	 */
	function table($str)
	{
		$this->table = (string) $str;
	}

	/**
	 * Limit
	 *
	 * @param integer $limit Limit records
	 * @param integer $offset Records offset
	 * @return void
	 */
	function limit($limit, $offset = 0)
	{
		$limit = (int) $limit;
		$offset = (int) $offset;

		if($limit > 0) {
			$this->sql_limit = "LIMIT $limit OFFSET $offset ";
		}
	}

	/**
	 * Sorting DESC
	 *
	 * @param string $column Sort by column
	 * @return void
	 */
	function desc($column = '')
	{
		if(empty($column)) {
			$column = $this->id;
		}
		$this->sql_order = " ORDER BY $column DESC";
	}

	/**
	 * Sorting ASC
	 *
	 * @param string $column Sort by column
	 * @return void
	 */
	function asc($column = '')
	{
		if(empty($column)) {
			$column = $this->id;
		}
		$this->sql_order = " ORDER BY $column ASC";
	}

	/**
	 * Regex search string
	 *
	 * @param string $str Search strings
	 * @return void
	 */
	function search($str)
	{
		$this->search = str_replace(" ", "|", (string) $str);
		if(!empty($this->search)) {
			$this->params[':regexp'] = trim($this->search," %");
			$this->sql_search = "WHERE CONCAT_WS(' ',".implode(",",$this->columns).") REGEXP :regexp ";
		}
	}

	/**
	 * Join sql
	 *
	 * @param string $sql Mysql query sql join part:
	 * LEFT JOIN user_token ON user.id = user_token.user_id
	 *
	 * @return void
	 */
	function join($sql)
	{
		if(!empty($sql)) {
			$this->sql_join = (string) $sql;
		}
	}

	/**
	 * Select rows list
	 *
	 * @param string $columns  Select query column list coma separated:
	 * user.*, user_token.token
	 *
	 * @return void
	 */
	function select($columns = '*')
	{
		if(!empty($columns)) {
			$this->sql_columns = (string) $columns;
		}
	}

	/**
	 * Allow columns
	 *
	 * @param array $arr Update, search allowed columns
	 * @return void
	 */
	function columns($arr)
	{
		$this->columns = (array) $arr;
	}

	/**
	 * Get record with id
	 *
	 * @param int $id Row id
	 *
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
	 *
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
	 *
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
	 *
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
	 *
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
	 *
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
	 *
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
	 * Add record to table
	 *
	 * @param array $arr Post array
	 *
	 * @return int Last inserted id
	 */
	function add(array $arr)
	{
		// Override this method
		throw new Exception("OVERRIDE_MODEL_ADD_METHOD", 402);

		return 0;
	}
}

/*
<?php
namespace App\Http;

use Exception;
use Furo\Model as BaseModel;

class Addon extends BaseModel
{
	function __construct()
	{
		$this->table('addon');
		$this->columns(['name','peice','price_sale','onsale']);
	}

	function add(array $arr)
	{
		// Override this method
		throw new Exception("ERR_ADD_METHOD", 400);
		return 0;
	}
}

$m = new Addon(); // or Model
$d = $m->get(1);
$m->search('min ser ker');
$m->limit(2,0);
$m->asc();
$m->update(['name' => 'ADMIN'], 1);
$m->select("user.*, user_token.token");
$m->join("LEFT JOIN user_token ON user.id = user_token.user_id");
$d = $m->all();
echo $m->count();
print_r($d);
*/