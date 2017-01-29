<?php
namespace bethge\SystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use bethge\SystemBundle\Components\ExceptionTrace;

use \Exception;

class DefaultController extends Controller
{	
	const DEFAULT_METHOD_LIST = 'list';
	const DEFAULT_METHOD_CREATE = 'create';
	const DEFAULT_METHOD_EDIT = 'edit';
	const DEFAULT_METHOD_SAVE = 'save';
	const DEFAULT_METHOD_DELETE = 'delete';
		
	public function handleAction($version, $namespace, $service, $method)
	{
		$params = $this->container->get('request_stack')->getCurrentRequest()->request->all();
		
		
		$response = null;
		try {
			$serviceModel = $this->container->getParameter('servicemodel');
			$serviceDef = $serviceModel[$version][$namespace][$service];
			$serviceName = $service;
			// build the crud manager
			$manager = $this->container->get($serviceDef['crudManager']);
			$manager->selectClassname($serviceDef['boClassname']);
			// build the service
			$service = new $serviceDef['serviceClassname']($manager);
			
			$this->title = 'DefaultController - service model version: ' . $version;

			switch ($method) {
				case self::DEFAULT_METHOD_LIST:
					$domainResult = $service->findAll();
					$response = $this->render(
						'SystemBundle:default:list.html.twig', 
						array(
							'title' => $this->title, 
							'namespace' => $namespace,
							'serviceName' => $serviceName,
							'version' => $version,
							'objs' => $domainResult
						)
					);
					break;
				case self::DEFAULT_METHOD_CREATE:
					$domainResult = $service->create();
					$response = $this->render(
						'SystemBundle:default:edit.html.twig', 
						array(
							'title' => $this->title,
							'namespace' => $namespace, 
							'serviceName' => $serviceName,
							'version' => $version, 
							'obj' => $domainResult
						)
					);		
					break;
				case self::DEFAULT_METHOD_EDIT:
					// $params = null|integer
					$domainResult = $service->find($params);
					$response = $this->render(
						'SystemBundle:default:edit.html.twig', 
						array(
							'title' => $this->title,
							'namespace' => $namespace, 
							'serviceName' => $serviceName,
							'version' => $version, 
							'obj' => $domainResult
						)
					);		
					break;
				case self::DEFAULT_METHOD_SAVE:
					
					$domainResult = $service->save($params['id'], $params);
					// redirect to list
					$response = $this->redirect('/' . $version . '/' . $namespace . '/' . $serviceName . '/' . self::DEFAULT_METHOD_LIST);
					break;
				case self::DEFAULT_METHOD_DELETE:
					// $params = integer[;integer]
					$ids = explode(';', trim($params, ';'));
					$service->delete($ids);
					// redirect to list
					$response = $this->redirect('/' . $version . '/' . $namespace . '/' . $serviceName . '/' . self::DEFAULT_METHOD_LIST);
					break;
				default:
					throw new Exception('Unknown method: ' . $method); 
			}
		} catch (Exception $e) {
			$response = $this->render(
				'SystemBundle:default:error.html.twig', 
				array(
					'title' => $this->title, 
					'serviceName' => $serviceName, 
					'message' => $e->getMessage(),
					'trace' => new ExceptionTrace($e->getTrace())
				)
			);
		}
		return $response;
	}
	
	public function getContainer()
	{
		return $this->container;
	}
	
	public function isGranted($attributes, $object = null)
	{
		return $this->isGranted($attributes, $object);
	}
	
	public function isCsrfTokenValid($id, $token)
	{
		$this->isCsrfTokenValid($id, $token);
	}    
}
