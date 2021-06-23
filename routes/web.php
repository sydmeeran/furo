<?php
use Furo\Db;
use Furo\Mail;
use Furo\Router;
use Furo\Response;
use Furo\Entities\Status;
use Exception;

try
{
	/* Settings */

	// Db
	Db::user('root')->pass('toor')->host('localhost')->database('app');

	// Db redis
	Db::redisPort('6379')->redisHost('localhost');

	// Smtp
	Mail::from('no-reply@furo.xx', 'Furo email');
	// Mail::debug();

	/* Routes */

	// Get
	Router::get('/', 'App\Http\Home', 'Index');

	// Get with middleware
	Router::get('/panel', 'App\Http\Home', 'Index', [ 'App\Middleware\Middleware::IsLogged' ]);
	// Auth bearer token
	Router::get('/auth', 'App\Http\Home', 'Index', [ 'App\Middleware\Middleware::AuthToken', 'App\Middleware\Middleware::Log' ]);

	// Get url params
	Router::get('/user/{id}', 'App\Http\Home', 'Index');
	Router::get('/user/{id}/post/{name}', 'App\Http\Home', 'Index', [ 'App\Middleware\Middleware::SetLoggedUser' ]);

	// Api auth
	Router::post('/client/login', 'App\Http\Api\Client\Auth', 'SignIn');
	Router::post('/client/register', 'App\Http\Api\Client\Auth', 'SignUp');
	Router::post('/client/password', 'App\Http\Api\Client\Auth', 'Password');
	Router::get('/client/activation/{code}', 'App\Http\Api\Client\Auth', 'Activation');

	// Api dev only testing (show logged user seeion data) !!!
	// Router::post('/client/active', 'App\Http\Api\Client\Auth', 'Session');

	// Error page
	Router::error('App\Entities\ErrorPage', 'Index');

	// Run ruoter
	Router::run();
}
catch(Exception $e)
{
	echo Response::httpError($e)::jsonStatus();
}
?>