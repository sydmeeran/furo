<?php
namespace Furo\Entities;

use Furo\Entities\SetGet;

class Route extends SetGet
{
	protected $path;
	protected $class;
	protected $method;
	protected $request;
	protected $middleware = [];

	function __construct($path, $class, $method, $middleware = [], $request = 'GET')
	{
		$this->path = $path;
		$this->class = $class;
		$this->method = $method;
		$this->request = $request;
		$this->middleware = $middleware;
	}
}