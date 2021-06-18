<?php
namespace App\Model;

use Furo\Db;

class Page
{
	protected $id = 'id';
	protected $sort = 'DESC';
	protected $page = 1;
	protected $limit = 0;
	protected $offset = 0;
	protected $fields = ['username','name','email','location'];

	function rows($table, $search = ''): array
	{
		if(!empty($search)) {
			return $this->getRowsSearch($table, $search);
		} else {
			return $this->getRows($table);
		}
	}

	function fields(array $arr)
	{
		$this->fields = $arr;
		return $this;
	}

	function id(string $str)
	{
		$this->id = $str;
		return $this;
	}

	function asc()
	{
		$this->sort = 'ASC';
		return $this;
	}

	function desc()
	{
		$this->sort = 'DESC';
		return $this;
	}

	function page(int $nr)
	{
		if($nr < 1) { return 0; }
		$this->page = $nr - 1;
		return $this;
	}

	function limit(int $val)
	{
		if($val < 1) { return 0; }
		$this->limit = $val;
		return $this;
	}

	protected function offset(): int
	{
		return $this->offset = $this->limit * $this->page;
	}

	protected function maxpage(int $max_rows): int
	{
		if($max_rows < 1) { return 1; }
		$pages = (int) ($max_rows / $this->limit);
		if($this->limit > 0) { $odd = $max_rows % $this->limit; }
		if($odd > 0) { $pages += 1; }
		if($pages < 1) { $pages = 1; }
		return $pages;
	}

	protected function getRows($table): array
	{
		$arr['max_rows'] = $this->countRows($table);
		$arr['max_pages'] = $this->maxpage($arr['max_rows']);
		$arr['page'] = $this->page + 1;
		$arr['offset'] = $this->offset();
		$arr['limit'] = $this->limit;
		$arr['rows'] = $this->dbRows($table);
		return $arr;
	}

	protected function getRowsSearch($table, $search): array
	{
		$arr['max_rows'] = $this->countRowsSearch($table,$search);
		$arr['max_pages'] = $this->maxpage($arr['max_rows']);
		$arr['page'] = $this->page + 1;
		$arr['offset'] = $this->offset();
		$arr['limit'] = $this->limit;
		$arr['search'] = $search;
		$arr['rows'] = $this->dbRowsSearch($table,$search);
		return $arr;
	}

	protected function dbRows($table): array
	{
		$l = '';
		if($this->limit > 0) {
			$l = " LIMIT $this->limit OFFSET $this->offset";
		}

		return Db::query("SELECT * FROM $table ORDER BY $this->id $this->sort $l", [])->fetchAllObj();
	}

	protected function dbRowsSearch($table, $search): array
	{
		$l = '';
		if($this->limit > 0) {
			$l = "LIMIT $this->limit OFFSET $this->offset";
		}
		$fields = $this->implode($this->fields);
		return Db::query(
			"SELECT * FROM $table WHERE CONCAT($fields) REGEXP :search ORDER BY $this->id $this->sort $l",
			[':search' => $this->filter($search)]
		)->fetchAllObj();
	}

	protected function countRows($table): int
	{
		$o = Db::query("SELECT COUNT(*) as cnt FROM $table", [])->fetchObj();
		return (int) $o->cnt;
	}

	protected function countRowsSearch($table, $search): int
	{

		$fields = $this->implode($this->fields);
		$o = Db::query(
			"SELECT COUNT(*) as cnt FROM $table WHERE CONCAT($fields) REGEXP :search",
			[':search' => $this->filter($search)]
		)->fetchObj();
		return (int) $o->cnt;
	}

	protected function filter($str)
	{
		return str_replace(" ", "|", $str);
	}

	protected function implode($arr)
	{
		return implode(',',$arr);
	}
}