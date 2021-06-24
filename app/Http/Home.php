<?php
namespace App\Http;

use Furo\Db;
use Furo\Mail;
use Furo\Request;
use Furo\Response;
use Furo\Img\ResizeImage;
use Exception;

class Home
{
	function Index()
	{
		$ex = null;
		$image = '';

		try
		{
			$rows = Db::query(
				"SELECT * FROM user WHERE id != :id ORDER BY id DESC LIMIT 2",
				[':id' => 0]
			)->fetchAllObj();

			// Unique image path
			$res = new ResizeImage('marker.png');
			$image = $res->uploadPath('marker.webp', false);

			// Send email
			$html = Mail::theme('App\Entities\EmailTheme', 'Welcome', ['{USER}' => 'Marry Doe']);
			Mail::send('fresh@woo.xx','Welcome email', $html);

		} catch (Exception $e) {
			$ex = $e;
		}

		return Response::httpError($ex)::jsonStatus([
			'response' => [
				'name' => 'Furo',
				'desc' => 'Hello from php router!',
				'unique_image' => $image,
				'url_id' => Request::urlParam('id'),
				'url_name' => Request::urlParam('name'),
				'query_id' => Request::get('id'),
				'logged_user' => Request::getEnv('user'),
				'bearer' => Request::bearerToken(),
				'rows' => $rows
			]
		]);
	}
}

/*
// Fetch rows with cache
// $rows = Db::queryCache("SELECT * FROM user WHERE id != :id", [':id' => 0]);
// Fetch rows with cache custom class
// $rows = Db::queryCache("SELECT * FROM user WHERE id != :id", [':id' => 0], false, "App\Model\User");
// Fetch row with cache
// $rows = Db::queryCache("SELECT * FROM user WHERE id != :id", [':id' => 0], true);

// Transaction
// $rows = Db::transaction(["SELECT * FROM user WHERE id != :id ORDER BY id DESC LIMIT 2", "SELECT SLEEP(1)"], [[':id' => 0]])->fetchAllObj("App\Model\UserModel");
// Lock tables with transaction
// $rows = Db::transaction(["UPDATE user SET about = 'Driver'", "SELECT * FROM user WHERE id != :id ORDER BY id DESC LIMIT 2"], [[], [':id' => 0]], 'LOCK TABLES user WRITE')->fetchAllObj("App\Model\UserModel");

// Fetch custom class obj
// $rows = Db::query("SELECT * FROM user WHERE id != :id ORDER BY id DESC LIMIT 2", [':id' => 0])->fetchAllObj("App\Model\UserModel");
// Fetch obj
*/