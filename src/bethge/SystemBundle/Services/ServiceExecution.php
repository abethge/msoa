<?php
namespace bethge\SystemBundle\Services;

use bethge\SystemBundle\Exceptions\BadRequestException;

use \ReflectionMethod;
use \Exception;


class ServiceExecution 
{
	public static function execute(IService $service, $method, array $inputParams = null)
	{
		// check and deserialize the posted parameters
		$methodParams = self::buildParamList(
			get_class($service),
			$method,
			$inputParams
		);
		// call service method
		return call_user_func_array(array($service, $method), $methodParams);
	}
	
	private static function buildParamList($serviceName, $methodName, array $paramList = null)
	{
		if (is_null($paramList)) {
			$paramList = array();
		}
		$refParams = self::reflectParameters($serviceName, $methodName);
		$inputNames = array_keys($paramList);
		$counter = 0;
		foreach ($refParams as $refParam) {
			if ($counter >= count($inputNames)) {
				throw new BadRequestException('additional parameters found');
			}
			if ($refParam->getName() != $inputNames[$counter]) {
				throw new BadRequestException('unknown or wrong placed parameter ' . $inputNames[$counter]);
			}
			$counter++;
		}
		return $paramList;
	}
	
	private static function reflectParameters($serviceName, $methodName)
	{
		if (substr($methodName, 0, 2) == '__') { // supress magic methods and constructors - needed?
			throw new BadRequestException('method starts with \"__\"');
		}
		try {
			$refMethod = new ReflectionMethod($serviceName, $methodName);
		} catch (Exception $e) {
			throw new BadRequestException();
		}
		return $refMethod->getParameters();
	}
}