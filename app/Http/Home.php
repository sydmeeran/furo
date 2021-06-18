<?php
namespace App\Http;

use Furo\Db;
use Furo\Mail;
use Furo\Request;
use Furo\Response;
use Furo\Entities\Status;
use App\Model\Page;
use Exception;
use Furo\Img\ResizeImage;

class Home
{
	function Index()
	{
		$resize = new ResizeImage('/home/boo/www/furo.xx/public/marker.png');
		// resize
		$resize->resizeTo(100, 100, 'maxWidth');
		// $resize->resizeTo(100, 100, 'maxHeight');
		// $resize->resizeTo(100, 100, 'exact');
		// $resize->resizeTo(100, 100);
		// save
		$resize->save('/home/boo/www/furo.xx/public/marker.webp');
		// download
		// $resize->save('images/be-original-exact.jpg', "100", true);

		$httpCode = 200;
		$statusCode = Status::OK;
		$statusMsg = 'users_info';

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
			$rows = Db::query("SELECT * FROM user WHERE id != :id ORDER BY id DESC LIMIT 2", [':id' => 0])->fetchAllObj("App\Model\UserModel");

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
			'url_id' => Request::urlParam('id'),
			'url_name' => Request::urlParam('name'),
			'query_id' => Request::get('id'),
			'logged_user' => Request::getEnv('logged_user'),
			'bearer' => Request::bearerToken(),
			'data' => $rows
		]);
	}
}