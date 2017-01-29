<?php
namespace bethge\SystemBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use bethge\SystemBundle\Services\ServiceDefinition;
use bethge\SystemBundle\Services\ServiceExecution;
use bethge\SystemBundle\Exceptions\BadRequestException;
use bethge\SystemBundle\Exceptions\UnknownSerializerException;

use \Exception;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class RpcController extends Controller
{	 
	public function handleAction($format, $version, $namespace, $service, $method)
	{
		try {
			// get the current request
			$request = $this->container->get('request_stack')->getCurrentRequest();
			// create service definition from servicemodel.yml which is included into config.yml
			$serviceDefinition = new ServiceDefinition(
				$this->container->getParameter('servicemodel'), $version, $namespace, $service
			);
			// create the service
			$serviceObject = $this->createService($serviceDefinition);
			// read method parameters from the request
			$inputParamsArray = $request->get('methodParams');
			// execute service
			$serviceResult = ServiceExecution::execute($serviceObject, $method, $inputParamsArray);
			// create serializer
			try {
				$serializer = $this->createSerializer($format);
			} catch (ServiceNotFoundException $e) {
				throw new UnknownSerializerException($format);
			}
			// return serialized service response
			$responseTemplateArray = $request->get('responseTemplate');
			return new Response($serializer->serialize($serviceResult, $responseTemplateArray), 200);
		} catch (UnknownSerializerException $e) {
			// can't create an expected error response -> set status to 400 'Bad rquest'
			return new Response($e->getMessage(), 400);
		} catch (BadRequestException $e) {
			$rpcException = $this->container->get('System.RpcException')->create($e, 'BAD_REQUEST');
			// create and return serialized exception response
			$serializer = $this->createSerializer($format);
			return new Response($serializer->serialize($rpcException));
		} catch (Exception $e) {
			$rpcException = $this->container->get('System.RpcException')->create($e, 'GENERAL_EXCEPTION');
			// create and return serialized exception response
			$serializer = $this->createSerializer($format);
			return new Response($serializer->serialize($rpcException));
		}
	}
	
	/**
	 * Factory method that creates the service as defined in the ServiceDefinition.
	 * 
	 * @param ServiceDefinition $serviceDefinition
	 * 
	 * @return IService (BasicCrudService)
	 */
	private function createService(ServiceDefinition $serviceDefinition)
	{
		// fetch the persistence manager from the container
		$crudManager = $this->container->get($serviceDefinition->getCrudManagerClassname());
		// create service as defined in servicemodel.yml
		$serviceClassname = $serviceDefinition->getServiceClassname();
		if (!class_exists($serviceClassname)) {
			throw new Exception('Unknown business service class: ' . $serviceClassname);
		}
		return new $serviceClassname(
			$crudManager,
			$serviceDefinition->getBusinessObjectClassname()
		);
	}
	
	private function createSerializer($format)
	{
		return $this->container->get('System.' . ucfirst($format) . 'Serializer');
	}
	
	/* not yet
	public function isGranted($attributes, $object = null)
	{
		return $this->isGranted($attributes, $object);
	}
	
	public function isCsrfTokenValid($id, $token)
	{
		$this->isCsrfTokenValid($id, $token);
	}    
	*/
}
