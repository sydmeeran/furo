<?php
namespace App\Entities;

use Exception;

class ErrorPage
{
    function Index()
    {
		throw new Exception("ERR_PAGE_ERROR_404", 404);
    }
}