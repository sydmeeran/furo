<?php
namespace Furo;

use Exception;
use Furo\Entities\Header;

class Response
{
	static $httpCode = 200;
	static $httpMessage = 'OK';

	static function json($arr)
	{
		Header::json();
		return json_encode($arr);
	}

	static function jsonStatus($arr = [])
	{
		Header::json();
		$arr['status'] = [
			'code' => self::$httpCode,
			'message' => self::$httpMessage
		];

		return json_encode($arr);
	}

	static function cors($arr)
	{
		Header::cors();
		return json_encode($arr);
	}

	static function corsJson($arr)
	{
		Header::corsJson();
		return json_encode($arr);
	}

	static function html($str)
	{
		Header::html();
		return $str;
	}

	static function text($str)
	{
		Header::text();
		return $str;
	}

	static function header($str)
	{
		header($str);
		return new self();
	}

	static function httpError(?Exception $ex)
	{
		if(is_a($ex, 'Exception')) {
			self::$httpCode = $ex->getCode();
			self::$httpMessage = $ex->getMessage();
			self::httpCode(self::$httpCode, self::$httpMessage);
		}
		return new self();
	}

	static function httpCode($code = 200, $error_msg = '')
	{
		$code = (string) $code;
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

		if ($code != null) {
			switch ($code) {
				case 100: $text = 'Continue'; break;
				case 101: $text = 'Switching Protocols'; break;
				case 102: $text = 'Processing'; break;
				case 200: $text = 'OK'; break;
				case 201: $text = 'Created'; break;
				case 202: $text = 'Accepted'; break;
				case 203: $text = 'Non-Authoritative Information'; break;
				case 204: $text = 'No Content'; break;
				case 205: $text = 'Reset Content'; break;
				case 206: $text = 'Partial Content'; break;
				case 207: $text = 'Multi-Status'; break;
				case 300: $text = 'Multiple Choices'; break;
				case 301: $text = 'Moved Permanently'; break;
				case 302: $text = 'Moved Temporarily'; break;
				case 303: $text = 'See Other'; break;
				case 304: $text = 'Not Modified'; break;
				case 305: $text = 'Use Proxy'; break;
				case 306: $text = '(Unused)'; break;
				case 307: $text = 'Temporary Redirect'; break;
				case 308: $text = 'Permanent Redirect'; break;
				case 400: $text = 'Bad Request'; break;
				case 401: $text = 'Unauthorized'; break;
				case 402: $text = 'Payment Required'; break;
				case 403: $text = 'Forbidden'; break;
				case 404: $text = 'Not Found'; break;
				case 405: $text = 'Method Not Allowed'; break;
				case 406: $text = 'Not Acceptable'; break;
				case 407: $text = 'Proxy Authentication Required'; break;
				case 408: $text = 'Request Time-out'; break;
				case 409: $text = 'Conflict'; break;
				case 410: $text = 'Gone'; break;
				case 411: $text = 'Length Required'; break;
				case 412: $text = 'Precondition Failed'; break;
				case 413: $text = 'Request Entity Too Large'; break;
				case 414: $text = 'Request-URI Too Large'; break;
				case 415: $text = 'Unsupported Media Type'; break;
				case 416: $text = 'Requested Range Not Satisfiable'; break;
				case 417: $text = 'Expectation Failed'; break;
				case 418: $text = 'Im a teapot'; break;
				case 419: $text = 'Authentication Timeout'; break;
				case 420: $text = 'Enhance Your Calm'; break;
				case 421: $text = 'Misdirected Request'; break;
				case 422: $text = 'Unprocessable Entity'; break;
				case 423: $text = 'Locked'; break;
				case 424: $text = 'Failed Dependency'; break;
				case 425: $text = 'Unordered Collection'; break;
				case 426: $text = 'Upgrade Required'; break;
				case 428: $text = 'Precondition Required'; break;
				case 429: $text = 'Too Many Requests'; break;
				case 431: $text = 'Request Header Fields Too Large'; break;
				case 444: $text = 'No Response'; break;
				case 449: $text = 'Retry With'; break;
				case 450: $text = 'Blocked by Windows Parental Controls'; break;
				case 451: $text = 'Unavailable For Legal Reasons'; break;
				case 494: $text = 'Request Header Too Large'; break;
				case 495: $text = 'Cert Error'; break;
				case 496: $text = 'No Cert'; break;
				case 497: $text = 'HTTP to HTTPS'; break;
				case 499: $text = 'Client Closed Request'; break;
				case 500: $text = 'Internal Server Error'; break;
				case 501: $text = 'Not Implemented'; break;
				case 502: $text = 'Bad Gateway'; break;
				case 503: $text = 'Service Unavailable'; break;
				case 504: $text = 'Gateway Time-out'; break;
				case 505: $text = 'HTTP Version not supported'; break;
				case 506: $text = 'Variant Also Negotiates'; break;
				case 507: $text = 'Insufficient Storage'; break;
				case 508: $text = 'Loop Detected'; break;
				case 509: $text = 'Bandwidth Limit Exceeded'; break;
				case 510: $text = 'Not Extended'; break;
				case 511: $text = 'Network Authentication Required'; break;
				case 598: $text = 'Network read timeout error'; break;
				case 599: $text = 'Network connect timeout error'; break;
				// Default errors: mysql, mail etc.
				default:
					$text = "Unprocessable Entity ($code)";
					if(!empty($error_msg)) {
						$text = $error_msg;
					}
					$code = 422;
				break;
			}

			header($protocol . ' ' . $code . ' ' . $text);
		}

		return new self();
	}
}