<?php
namespace App\Http\Sample;

use Exception;
use Furo\Response;
use App\Model\Sample\SampleModel;

/**
 * SampleController controller class
 */
class SampleController
{
	function GetId()
	{
		$ex = null;
		$msg = '';

		try
		{
			// Middleware (authenticate user first)
			$user = $_SESSION['user'];
			// Model
			$o = new SampleModel();
			$msg = $o->get($user->id);
		}
		catch (Exception $e) {
			$ex = $e;
			$msg = 'invalid_id';
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'profil' => $msg
			]
		]);
	}

	function Get()
	{
		$ex = null;
		$msg = '';

		try
		{
			// Middleware
			$user = $_SESSION['user'];
			// Model
			$o = new SampleModel();
			$o->limit($_POST['limit'],$_POST['offset']);
			$o->search($_POST['search']);
			$msg = $o->all();
		}
		catch (Exception $e) {
			$ex = $e;
			$msg = null;
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'profil' => $msg
			]
		]);
	}

	function Update()
	{
		$ex = null;
		$msg = 'updated';

		try
		{
			// Middleware
			$user = $_SESSION['user'];
			// Model
			$o = new SampleModel();
			// Validate variables with model class
			foreach ($_POST as $k => $v) {
				$o->$k = $v;
			}
			// Db
			$o->update($_POST, $user->id);
		}
		catch (Exception $e) {
			$ex = $e;
			$msg = 'update_error';
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'profil' => $msg
			]
		]);
	}

	function Insert()
	{
		$ex = null;
		$msg = 'created';

		try
		{
			// Middleware
			$user = $_SESSION['user'];
			// Model
			$o = new SampleModel();
			// Validate variables with model class
			foreach ($_POST as $k => $v) {
				$o->$k = $v;
			}
			// Add logged user id
			$o->user_id = (int) $user->id;
			// Create new user in db
			$o->insert($o->variables());
		}
		catch (Exception $e) {
			$ex = $e;
			$msg = 'create_error';
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'profil' => $msg
			]
		]);
	}

	function Delete()
	{
		$ex = null;
		$msg = 'deleted';

		try
		{
			// Middleware
			$user = $_SESSION['user'];
			// Validate
			if($user->role != 'admin' && $user->role != 'worker') {
				// Model
				$o = new SampleModel();
				$msg = $o->delete($user->id);
			}
		}
		catch (Exception $e) {
			$ex = $e;
			$msg = 'not_deleted';
		}

		return Response::httpError($ex)::jsonStatus([
			'res' => [
				'profil' => $msg
			]
		]);
	}
}