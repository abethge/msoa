<?php
namespace bethge\SystemBundle\Exceptions;

use \Exception;

class ObjectNotFoundException extends Exception
{
	public function __construct()
	{
		parent::__construct(
			"Object not found."
		);
	}
}