<?php
namespace Furo\Entities;

use Exception;

class ErrorPage
{
    function Index()
    {
		throw new Exception("ERR_PAGE", 404);
    }
}