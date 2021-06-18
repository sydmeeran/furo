<?php
namespace App\Model;

use Exception;
use Furo\Db;
use Furo\Entities\SetGet;

class Address extends SetGet
{
	public $address_id;
	public $user_id;
	public $firstname;
	public $lastname;
	public $country;
	public $street;
	public $district;
	public $city;
	public $zip;
	public $phone;
	public $email;
	public $lng;
	public $lat;
	public $time;

	protected $allowedColumns = [];

	// Update user table columns
	public function update($arr, $user_id)
	{
		if($user_id > 0) {
			foreach ($arr as $k => $v) {
				if(property_exists(UserModel::class, $k)) {
					Db::query("UPDATE address SET $k = :v WHERE user_id = :id", [':v' => $v, ':id' => $user_id]);
				} else {
					throw new Exception("ERR_TABLE_COLUMN", 422);
				}
			}
		} else {
			throw new Exception("ERR_USER_ID", 402);
		}
	}
}