<?php
namespace bethge\SystemBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use bethge\SystemBundle\Components\ExceptionTrace;

use bethge\SystemBundle\Controller\ReflectionApi;
use bethge\SystemBundle\Services\ServiceDefinition;
use bethge\SystemBundle\Services\ServiceExecution;

use \Exception;
use \ReflectionMethod;

class RpcTestApiController extends Controller
{	
	public function indexAction($version = null, $namespace = null, $service = null, $method = null)
	{	
		try {
			$serviceModel = $this->container->getParameter('servicemodel');
			$serviceDefinition = new ServiceDefinition($serviceModel, $version, $namespace, $service);
			$list = null;
			$params = null;
			if ($version) {
				if ($namespace) {
					if ($service) {
						if ($method) {
							// show attributes form
							$params = ReflectionApi::getParameters(
								$serviceDefinition->getServiceClassname(), 
								$method
							);
						} else {
							// list methods
							$refMethods = ReflectionApi::getMethods($serviceDefinition->getServiceClassname());
							$list = array();
							foreach ($refMethods as $refMethod) {
								$list[] = $refMethod->getName();
							}
						}
					} else {
						// list services
						$list = $serviceDefinition->getServices();									}
				} else {
					// list namespaces
					$list = $serviceDefinition->getNamespaces();
				}
			} else {
				// list versions
				$list = $serviceDefinition->getVersions();				
			}
			
			return $this->render('SystemBundle:default:jsonrpc.html.twig', 
				array(
					'title' => 'API Catalog',
					'namespace' => $namespace,
					'service' => $service,
					'version' => $version,
					'method' => $method,
					'list' => $list,
					'params' => $params
				)
			);
		} catch (Exception $e) {
			$response = $this->render(
				'SystemBundle:default:error.html.twig',
				array(
					'title' => 'API Catalog',
					'serviceName' => $service,
					'message' => $e->getMessage(),
					'trace' => new ExceptionTrace($e->getTrace())
				)
			);
			return $response;
		}
	}
}
