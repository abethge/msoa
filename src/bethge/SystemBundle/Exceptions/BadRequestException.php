<?php
namespace bethge\SystemBundle\Exceptions;

use \Exception;

class BadRequestException extends Exception
{
	public function __construct($info = null)
	{
		parent::__construct(
			'Bad Request: ' . $info
		);
	}
}