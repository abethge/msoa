<?php
namespace bethge\SystemBundle\Entity;

use Doctrine\ORM\EntityRepository;
use bethge\SystemBundle\Interfaces\INodeRepository;

/**
 * NodeRepository
 */
class NodeRepository extends EntityRepository implements INodeRepository
{
	public function test()
	{
		return 'Test: Hello NodeRepository';
	}
}
