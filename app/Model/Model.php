<?php
namespace App\Model;

use Furo\Db;

class Model
{
	protected $tb_primary_id = 'id';
	protected $tb_name = 'user';
	protected $columns = [];

	/**
	 * Table autoincrement column name
	 *
	 * @param string $name Column name
	 * @return object Self object
	 */
	function id($col)
	{
		$this->validName($col);
		$this->tb_primary_id = $col;
		return new self();
	}

	/**
	 * Change table
	 *
	 * @param string $name Table name
	 * @return object Self object
	 */
	function table($name)
	{
		$this->validName($name);
		$this->table = $name;
		return new self();
	}

	/**
	 * Allowed columns for update
	 *
	 * @param array $arr Array with allowed columns
	 * @return object Self object
	 */
	public function columns(array $arr)
	{
		foreach ($arr as $k => $v) {
			$this->validName($k);
		}
		$this->columns = $arr;
		return new self();
	}

	/**
	 * Update table
	 *
	 * @param string $arr Array with data
	 * @param int $user_id User id
	 * @return void
	 *
	 * @throws Exception ERR_TABLE_COLUMN
	 * @throws Exception ERR_USER_ID
	 */
	public function update($arr, $user_id)
	{
		if($user_id > 0) {
			foreach ($arr as $k => $v) {
				if(!empty($this->columns)) {
					if(property_exists(self::class, $k) && in_array($k, $this->columns)) {
						Db::query("UPDATE $this->tb_name SET $k = :v WHERE $this->tb_primary_id = :id", [':v' => $v, ':id' => $user_id]);
					} else {
						throw new Exception("ERR_TABLE_COLUMN", 400);
					}
				} else {
					if(property_exists(self::class, $k)) {
						Db::query("UPDATE $tb_name SET $k = :v WHERE $this->tb_primary_id = :id", [':v' => $v, ':id' => $user_id]);
					} else {
						throw new Exception("ERR_TABLE_COLUMN", 400);
					}
				}
			}
		} else {
			throw new Exception("ERR_USER_ID", 400);
		}
	}

	/**
	 * Get row from table
	 *
	 * @param string $id Auti increment id
	 * @return void
	 */
	public function get($id)
	{
		if($id > 0) {
			return Db::query("SELECT * FROM $this->tb_name WHERE $this->tb_primary_id = :id", ['id' => $id])->fetchObj();
		} else {
			throw new Exception("ERR_USER_ID", 402);
		}
	}

	/**
	 * Get rows from table
	 *
	 * @param string $id Auti increment id
	 * @return void
	 */
	public function all($offset = 0, $limit = 0, $sort = 'desc', $search_str = '', $search_columns = [])
	{
		$sort = ($sort == 'desc') ? 'desc' : 'asc';

		if(!empty($search_str)) {
			$fields = $this->implode($search_columns);

			if($offset == 0 && $limit == 0) {
				return Db::query("SELECT * FROM $this->tb_name WHERE CONCAT($fields) REGEXP :search ORDER BY $this->tb_primary_id $sort", [':search' => $this->filter($search_str)])->fetchAllObj();
			}

			if($limit > 0) {
				return Db::query("SELECT * FROM $this->tb_name WHERE CONCAT($fields) REGEXP :search ORDER BY $this->tb_primary_id $sort LIMIT :limit OFFSET :offset", [':search' => $this->filter($search_str), ':limit' => $limit, ':offset' => $offset])->fetchAllObj();
			}
		} else {
			if($offset == 0 && $limit == 0) {
				return Db::query("SELECT * FROM $this->tb_name ORDER BY $this->tb_primary_id $sort", [])->fetchAllObj();
			}

			if($limit > 0) {
				return Db::query("SELECT * FROM $this->tb_name ORDER BY $this->tb_primary_id $sort LIMIT :limit OFFSET :offset", [':limit' => $limit, ':offset' => $offset])->fetchAllObj();
			}
		}
	}

	function filter($str)
	{
		return str_replace(" ", "|", $str);
	}

	function implode($arr)
	{
		return implode(',',$arr);
	}

	/**
	 * Test table name format
	 *
	 * @param string $str Table column name
	 * @return void
	 */
	function validName($str)
	{
		if(!preg_match('/^[A-z_]{1,}$/',$str)) {
			throw new Exception("ERR_FIELD_FORMAT", 402);
		}
	}
}