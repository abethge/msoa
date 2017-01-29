<?php
namespace bethge\SystemBundle\Components;
  

class ExceptionTrace
{
	private $trace;
	
	public function __construct(array $trace)
	{
		$this->trace = array();
		foreach ($trace as $entry) {
			$this->trace[] = new ExceptionTraceEntry($entry);
		}
	}
		
	public function getEntries()
	{
		return $this->trace;
	}
}