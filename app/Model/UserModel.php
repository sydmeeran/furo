<?php
namespace App\Model;

use Exception;
use Furo\Db;
use App\Model\User;

class UserModel extends User
{
	protected $allowedColumns = ['username', 'name', 'location', 'code', 'www', 'about', 'mobile'];

	// Update user table only allowed columns
	public function update($arr, $user_id)
	{
		if($user_id > 0) {
			foreach ($arr as $k => $v) {
				if(property_exists(UserModel::class, $k) && in_array($k, $this->allowedColumns)) {
					// Update user table column
					Db::query("UPDATE user SET $k = :v WHERE id = :id", [':v' => $v, ':id' => $user_id]);
				}
			}
		} else {
			throw new Exception("ERR_USER_ID", 402);
		}
	}
}