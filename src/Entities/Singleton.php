<?php
declare(strict_types=1);
namespace Furo\Entities;

class Singleton
{
	private static $instance = null;

	public static function getInstance(): self
	{
		// static $instance;
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}
}