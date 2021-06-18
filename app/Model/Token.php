<?php
namespace App\Model;

use Exception;
use Furo\Db;
use Furo\Entities\SetGet;

class Token extends SetGet
{
	public $token_id;
	public $user_id;
	public $hash;
	public $expires;
	public $created;

	protected $allowedColumns = ['hash', 'expires'];

	// Update user table columns
	public function update($arr, $user_id)
	{
		if($user_id > 0) {
			foreach ($arr as $k => $v) {
				if(property_exists(UserModel::class, $k)) {
					Db::query("UPDATE token SET $k = :v WHERE user_id = :id", [':v' => $v, ':id' => $user_id]);
				} else {
					throw new Exception("ERR_TABLE_COLUMN", 422);
				}
			}
		} else {
			throw new Exception("ERR_USER_ID", 402);
		}
	}
}