<?php
namespace bethge\SystemBundle\Components;


class ExceptionTraceEntry
{
	private $entry;
	
	public function __construct(array $entry)
	{
		$this->entry = $entry;
	}
	
	private function getProperty($key)
	{
		$ret = '';
		if (isset($this->entry[$key])) {
			$ret = $this->entry[$key];
		}
		return $ret;	
	}
	
	public function getClass()
	{
		return $this->getProperty('class');
	}
	
	public function getMethod()
	{
		return $this->getProperty('function');
	}
	
	public function getLine()
	{
		return $this->getProperty('line');
	}
}