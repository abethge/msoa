<?php
namespace bethge\SystemBundle\Exceptions;

use \Exception;

class WrongParameterException extends Exception
{
	public function __construct()
	{
		parent::__construct(
			"Wrong parameter."
		);
	}
}