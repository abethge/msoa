<?php
namespace bethge\SystemBundle\Components\Serializer;

use \Exception;
use \ReflectionClass;
use \ReflectionMethod;

class XmlSerializer implements ISerializer
{
	public function serialize($objectOrArrayOfObjects)
	{		
		return utf8_encode(
			xmlrpc_encode(
				self::arrayfy($objectOrArrayOfObjects)
			)
		);
	}
	
	private static function arrayfy($objectOrArrayOfObjects, array $attrs = null)
	{
		$ret = null;
		if (is_object($objectOrArrayOfObjects)) {
			$ret = self::arrayfyObject($objectOrArrayOfObjects, $attrs);
		} else if (is_array($objectOrArrayOfObjects)) {
			$ret = array();
			foreach ($objectOrArrayOfObjects as $object) {
				$ret[] = self::arrayfyObject($object, $attrs);
			}
		} else if (is_null($objectOrArrayOfObjects)) {
			return null;
		}else {
			throw new Exception('Null, object or array of objects expected.');
		}
		return $ret;
	}
	
	private static function arrayfyObject($object, array $attrs = null)
	{
		$ret = array();
		if (is_object($object)) {
			$reflector = new ReflectionClass($object);
			$methods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
			foreach ($methods as $method) {
				$methodName = $method->getName();
				if (substr($methodName, 0, 3) == 'get' && $method->getNumberOfParameters() == 0) {
					$attrName = lcfirst(substr($methodName, 3));
					if (!$attrs || in_array($attrName, $attrs)) {
						$ret[$attrName] = $object->$methodName();
					}
				}
			}
		} else {
			throw new Exception('Object expected.');
		}
		return $ret;
	}
}
