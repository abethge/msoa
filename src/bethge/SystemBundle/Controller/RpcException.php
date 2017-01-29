<?php
namespace bethge\SystemBundle\Controller;

use \Exception;

class RpcException
{	
	private $message;
	private $code;
	
	public function create(Exception $exception, $rpcExceptionCode)
	{
		$this->message = $exception->getMessage();
		$this->code = $rpcExceptionCode;
		return $this;
	}

	public function getMessage()
	{
		return $this->message;
	}
	
	public function getRpcExceptionCode()
	{
		return $this->code;
	}
}
