<?php
namespace bethge\SystemBundle\Controller;

use bethge\SystemBundle\Exceptions\BadRequestException;

use \ReflectionClass;
use \ReflectionMethod;


class ReflectionApi
{
	public static function getMethods($serviceName)
	{
		$ret = array();
		$reflector = new ReflectionClass($serviceName);
		$methods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method) {
			if (substr($method->getName(), 0, 2) == '__') { // supress magic methods and constructors
				continue;
			}
			$ret[] = $method;
		}
		return $ret;
	}
	
	public static function getParameters($serviceName, $methodName)
	{
		if (substr($methodName, 0, 2) == '__') { // supress magic methods and constructors
			throw new Exception('Reflection not allowed for ' . $serviceName . '::' . $methodName . '.');
		}
		$refMethod = new ReflectionMethod($serviceName, $methodName);
		return $refMethod->getParameters();
	}
}