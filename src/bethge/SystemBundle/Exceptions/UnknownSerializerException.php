<?php
namespace bethge\SystemBundle\Exceptions;

use \Exception;

class UnknownSerializerException extends Exception
{
	public function __construct($info = null)
	{
		parent::__construct(
			'Unknown serializer: ' . $info
		);
	}
}