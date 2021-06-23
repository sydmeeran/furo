<?php
namespace App\Http;

use Furo\Db;
use Furo\Mail;
use Furo\Request;
use Furo\Response;
use Furo\Entities\Status;
use Furo\Img\ResizeImage;
use App\Model\Page;
use Exception;

class Home
{
	function Index()
	{
		$httpCode = 200;
		$statusCode = Status::OK;
		$statusMsg = 'users_info';
		$time = time();
		$image = '';

		try {
			// Fetch rows with cache
			// $rows = Db::queryCache("SELECT * FROM user WHERE id != :id", [':id' => 0]);
			// Fetch rows with cache custom class
			// $rows = Db::queryCache("SELECT * FROM user WHERE id != :id", [':id' => 0], false, "App\Model\User");
			// Fetch row with cache
			// $rows = Db::queryCache("SELECT * FROM user WHERE id != :id", [':id' => 0], true);

			// Fetch obj
			// $rows = Db::query("SELECT * FROM user WHERE id != :id ORDER BY id DESC LIMIT 2", [':id' => 0])->fetchAllObj();
			// Fetch custom class obj
			// $rows = Db::query("SELECT * FROM user WHERE id != :id ORDER BY id DESC LIMIT 2", [':id' => 0])->fetchAllObj("App\Model\UserModel");

			// Transaction
			// $rows = Db::transaction(["SELECT * FROM user WHERE id != :id ORDER BY id DESC LIMIT 2", "SELECT SLEEP(1)"], [[':id' => 0]])->fetchAllObj("App\Model\UserModel");
			// Lock tables with transaction
			$rows = Db::transaction(["UPDATE user SET about = 'Driver $time'", "SELECT * FROM user WHERE id != :id ORDER BY id DESC LIMIT 2"], [[], [':id' => 0]], 'LOCK TABLES user WRITE')->fetchAllObj("App\Model\UserModel");

			// Update users name with rand value
			foreach ($rows as $k => $user) {
				if($user->role == 'driver') {
					$user->update([ 'code' => uniqid() ], $user->id);
				}
			}

			// Pagination with multi search
			$page = new Page();
			// $page->page(1)->limit(2); // sort desc
			// $rows = $page->rows('user');
			$page->page(2)->limit(2)->asc()->fields(['username','name','email']); // sort asc
			$rows = $page->rows('user', 'admin user worker driver');

			// Unique image path
			$res = new ResizeImage('marker.png');
			$image = $res->uploadPath('marker.webp', false);

			// Send email
			$html = Mail::theme('App\Entities\EmailTheme', 'Welcome', ['{USER}' => 'Marry Doe']);
			Mail::send('boo@woo.xx','Welcome email', $html);

		} catch (Exception $e) {
			$statusCode = Status::ERR;
			$statusMsg = $e->getMessage();
			$httpCode = $e->getCode();
		}

		return Response::httpCode($httpCode)::json([
			'status' => [
				'code' => $statusCode,
				'message' => $statusMsg
			],
			'name' => 'Furo',
			'desc' => 'Hello from php router!',
			'unique_image' => $image,
			'url_id' => Request::urlParam('id'),
			'url_name' => Request::urlParam('name'),
			'query_id' => Request::get('id'),
			'logged_user' => Request::getEnv('user'),
			'bearer' => Request::bearerToken(),
			'data' => $rows
		]);
	}
}