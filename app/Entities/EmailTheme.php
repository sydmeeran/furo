<?php
namespace App\Entities;

class EmailTheme
{
	function Welcome()
	{
		return '<!DOCTYPE html> <html> <h3> Hello {USER} ! </h3> <p> <a href="http://{DOMAIN}"> Welcome! </a> </p> </html>';
	}

	function Password()
	{
		return '<!DOCTYPE html> <html> <h1> Hello {EMAIL} </h1> <h3> Your new password: </h3> <strong> {PASS} </strong> </html>';
	}

	function Activation()
	{
		return '<!DOCTYPE html> <html> <h1> Welcome {EMAIL} </h1> <h3> Activate your account! </h3> <a href="http://{DOMAIN}/client/activation/{CODE}">Activation</a> </html>';
	}
}