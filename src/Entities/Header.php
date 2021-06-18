<?php
namespace Furo\Entities;

class Header
{
	static function bearerToken()
	{
		return trim(str_replace('Bearer ', '', self::getHeader()));
	}

	static function json()
	{
		header('Content-Type: application/json; charset=utf-8');
	}

	static function html()
	{
		header('Content-Type: text/html; charset=utf-8');
	}

	static function text()
	{
		header('Content-Type: text/plain; charset=utf-8');
	}

	static function auth($token)
	{
		header('Authorization: Bearer ' . $token);
	}

	static function imagePng()
	{
		header("Content-Type: image/png");
	}

	static function imageJpg()
	{
		header("Content-Type: image/png");
	}

	static function imageGif()
	{
		header("Content-Type: image/gif");
	}

	static function imageSvg()
	{
		header("Content-Type: image/svg+xml");
	}

	static function imageWebp()
	{
		header("Content-Type: image/webp");
	}

	static function content($type = 'text/html', $charset = 'utf-8')
	{
		header("Content-Type: " . $type . '; charset=' . $charset);
	}

	static function custom($header = 'Content-Type: text/html; charset=utf-8')
	{
		header($header);
	}

	static function noCache()
	{
		header('Expires: Sun, 25 Jul 1997 06:02:34 GMT');
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
	}

	static function cors($origin = '*', $seconds = 86400)
	{
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, Origin, X-Requested-With, X-Real-IP, Forwarded, X-Forwarded-For, X-Forwarded-Proto, X-Forwarded-Host, X-Sess-Id');
		header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
		header('Access-Control-Allow-Origin: ' . $origin);
		header('Access-Control-Max-Age: ' . $seconds);
	}

	static function corsJson()
	{
		self::json();
		self::cors();
	}

	static function redirect($url, $permanently = true)
	{
		if($permanently == true) {
			header("HTTP/1.1 301 Moved Permanently");
		}
		header('Location: ' . $url);
	}

	static function fromString($header)
	{
		header($header);
	}

	static function getHeader($name = 'Authorization')
	{
		$arr = getallheaders();
		if(!empty($arr[$name]))
		{
			return $arr[$name];
		}
		return '';
	}

	static function getHeaders()
	{
		return getallheaders();
	}

	static function uniqueToken($len = 32)
	{
		// 32 * 2 = 64 characters unique /^[0-9a-f]{64}$/
		return bin2hex(random_bytes($len));
	}

	static function uuid()
	{
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0C2f ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B )
		);
	}
}