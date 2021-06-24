# Furo php micro framework
Php micro framework for rest api with mysql database connection, routes middleware, smtp email with phpmailer and rest api session authentication.

### Install with git
```sh
# get with git
git clone https://github.com/wowpowhub/furo.git /var/www/html/furo.xx

# create user php-fpm pool
sudo useradd -m www-furo

# permissions
chown -R your-user-name:www-data /var/www/html/furo.xx
chmod -R 2775 /var/www/html/furo.xx

# update composer
cd /var/www/html/furo.xx
composer update --no-dev
composer dump-autoload -o --no-dev
```

### Routes
Routes in file: /public/routes.php
```php
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
	Db::user('root')->pass('toor')->host('localhost');

	// Db redis
	Db::redisPort('6379')->redisHost('localhost');

	// Smtp
	Mail::from('no-reply@furo.xx','Furo email');
	// Mail::from('no-reply@furo.xx','Furo email')->host('localhost')->port(25)->auth()->user('')->pass('')->tls();

	/* Routes */

	// Get
	Router::get('/', 'App\Http\Home', 'Index');

	// Get with middleware
	Router::get('/auth', 'App\Http\Home', 'Index', [ 'App\Middleware\Middleware::Auth', 'App\Middleware\Middleware::Log' ]);

	// Get params url: /user/UserAlias/post/first-post-name?id=321
	Router::get('/user/{id}', 'App\Http\Home', 'Index');
	Router::get('/user/{id}/post/{name}', 'App\Http\Home', 'Index');

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
```

### Controller
```php
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
			Mail::send('boo@woo.xx','Welcome email', $html);

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
?>
```

### Db
```sql
# Db
CREATE DATABASE `app` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Mysql user
GRANT ALL ON app.* TO 'root'@'localhost' IDENTIFIED BY 'toor' WITH GRANT OPTION;
GRANT ALL ON app.* TO 'root'@'127.0.0.1' IDENTIFIED BY 'toor' WITH GRANT OPTION;
FLUSH PRIVILEGES;

# Illegal mix of collations error
ALTER DATABASE app DEFAULT COLLATE utf8_unicode_ci;
ALTER TABLE app.user CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
```

### Examples
See in public directory

### Run in browser
```
php -S localhost:8000 -t /var/www/html/furo.xx/public
```

### Local host domain
nano /etc/hosts
```
# Add line
127.0.0.1 www.furo.xx furo.xx
```

### Run with Nginx virtualhost
nano /etc/nginx/sites-available/default
```cnf
# Add to file
# /etc/nginx/sites-available/default

server {
	# port
	listen 80;
	listen [::]:80;
	# server
	server_name furo.xx;
	root /var/www/html/furo.xx/public;
	index index.php;
	# all files
	location / {
		try_files $uri $uri/ /index.php?url=$uri&$args;
	}
	# php files
	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php7.3-fpm.sock;
	}
	# cache
	location ~ /(Cache|cache|.cache|.git|sql|install|vendor) {
		deny all;
		return 404;
	}
	# settings
	charset utf-8;
	disable_symlinks off;
	client_max_body_size 100M;
	# tls redirect
	# return 301 https://$host$request_uri;
	# return 301 https://furo.xx$request_uri;
}
```

### Php-fpm, nginx test config
```sh
sudo php-fpm7.3 -t
sudo php-fpm7.3 -tt
sudo nginx -t
```

### Clear fastcgi cache
```php
<?php
# Purge cache php
shell_exec("rm -Rf /tmp/php_fastcgi_cache/*");
```

### Http error codes
```
2xx success status codes confirm that your request worked as expected
4xx error status codes indicate an error because of the information provided (e.g., a required parameter was omitted)
5xx error status codes are rare and indicate an error with servers
```

### All http error codes
https://github.com/wowpowhub/furo/blob/main/src/Response.php

### Curl examples
```sh
# Test api
curl -X POST -d 'pass=password&email=user@woo.xx' http://furo.xx/client/register
curl -X POST -d 'pass=password&email=user@woo.xx' -c /tmp/cookies.txt http://furo.xx/client/login -v
curl -X GET -d 'id=1' -b /tmp/cookies.txt http://furo.xx/user/BOOO/post/huuu-jejej?id=7771118888 -v

# Get
curl -H 'Accept: application/json' http://furo.xx/auth
# Token
curl -H "Authorization: Bearer Token-Here-012345" http://furo.xx/auth
# Post
curl -X POST -F 'name=Name' -F 'email=mail@ex.xx' http://furo.xx/user/4656
curl -X POST -d 'name=Name&email=mail@ex.xx' http://furo.xx/user/4656/post/Post_Name?id=123242
# Json
curl -X POST -H "Content-Type: application/json" -d '{"name":"Ben", "pass": "12345"}' http://furo.xx/user/123
curl -X POST -H "Authorization: Bearer Token-Here-012345" -H "Content-Type: application/json" -d '{"name":"Ben", "pass": "12345"}' http://furo.xx/user/info
curl -X POST -u "Token-Here-012345" -H "Content-Type: application/json" -d '{"name":"Ben", "pass": "12345"}' http://furo.xx/user/info
# Set cookies
curl -X POST -d 'pass=password&email=admin@furo.xx' -c /tmp/cookies.txt http://furo.xx/client/login
# Show headers
curl -X POST -d 'pass=password&email=admin@furo.xx' -c /tmp/cookies.txt http://php.xx/client/login -v
# Use cookie file
curl -X POST -d 'id=1' -b /tmp/cookies.txt http://furo.xx/client/active
# Test fastcgi cache: set and get with cookie
curl -X POST -c /tmp/cookie.txt http://furo.xx/user/1 -v -i
curl -X POST -b /tmp/cookie.txt http://furo.xx/user/1 -v -i
# Show headers and execution time
time curl -i http://furo.xx
```