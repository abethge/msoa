<?php
namespace bethge\SystemBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use bethge\SystemBundle\Services\ServiceDefinition;
use bethge\SystemBundle\Services\NodeService;
use bethge\SystemBundle\Exceptions\ObjectNotFoundException;


class NodeServiceTest extends WebTestCase
{
	private $service;
	
	public function __construct()
	{
		$client = static::createClient();
		$container = $client->getContainer();
		// create service definition from servicemodel.yml which is included into config.yml
		$serviceDefinition = new ServiceDefinition(
			$container->getParameter('servicemodel'),
			'1.0', 'System', 'Node'
		);
		// fetch the persistence manager from the container
		$crudManager = $container->get($serviceDefinition->getCrudManagerClassname());
		// create service as defined in servicemodel.yml
		$this->service = new NodeService(
			$crudManager,
			$serviceDefinition->getBusinessObjectClassname()
		);
	}
	
	public function testCreateSaveUpdateFindDelete()
	{
		$node = $this->service->create();
		$this->assertNotNull($node);
		
		$node = $this->service->update(array('name' => 'unittest node'));
		$node = $this->service->save($node);
		
		$this->assertEquals('unittest node', $node->getName());
		$this->assertNotNull($node->getId());
		
		$node = $this->service->find($node->getId());
		$this->assertNotNull($node);

		$id = $node->getId();
		$this->service->delete($id);
		$this->assertNull($node->getId());
		try {
			$node = $this->service->find($id);
		} catch (ObjectNotFoundException $e) {
			return;
		}
		$this->fail('ObjectNotFoundException expected.');
	}

}