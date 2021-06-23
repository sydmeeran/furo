<?php
namespace App\Model;

use Exception;
use Furo\Db;
use Furo\Entities\SetGet;

class User extends SetGet
{
	public $id;
	public $username;
	public $email;
	public $role;
	public $is_driver;
	public $name;
	public $location;
	public $mobile;
	public $about;
	public $www;
	public $status;
	public $time;
	public $pass;
	public $code;
	protected $allowedColumns = [];

	public function link()
	{
		return "<a href=\"/profile/$this->id\">$this->username</a>";
	}

	// Update user table columns
	public function update($arr, $user_id)
	{
		if($user_id > 0) {
			foreach ($arr as $k => $v) {
				if(property_exists(User::class, $k)) {
					Db::query("UPDATE user SET $k = :v WHERE id = :id", [':v' => $v, ':id' => $user_id]);
				} else {
					throw new Exception("ERR_TABLE_COLUMN", 422);
				}
			}
		} else {
			throw new Exception("ERR_USER_ID", 402);
		}
	}

	public function get($user_id, $clear_pass = true)
	{
		if($user_id > 0) {
			$u = Db::query("SELECT * FROM user WHERE id = :id", ['id' => $user_id])->fetchObj();
			if($clear_pass) {
				unset($u->pass);
			}
			return $u;
		} else {
			throw new Exception("ERR_USER_ID", 402);
		}
	}
}