<?php
namespace bethge\SystemBundle\Persister;

use Doctrine\ORM\EntityManager;

use bethge\SystemBundle\Interfaces\IPersistenceManager;
use bethge\SystemBundle\Interfaces\IBusinessObject;
use bethge\SystemBundle\Exceptions\ObjectNotFoundException;
use Doctrine\Common\Collections\Criteria;

use \DateTime;
use \ReflectionClass;


class DoctrineOrmPersistenceManager implements IPersistenceManager
{
	/**
	 * @var EntityManager entityManager 
	 */
	private $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	private function initObject(IBusinessObject $object)
	{
		$reflector = new ReflectionClass($object);
		$property = $reflector->getProperty('repository');
		$property->setAccessible(true);
		$property->setValue($object, $this->entityManager->getRepository(get_class($object)));
		$property->setAccessible(false);
	}
	
	public function beginTransaction()
	{
		$this->entityManager->getConnection()->beginTransaction();
	}
	
	public function commit()
	{
		$this->entityManager->getConnection()->commit();
	}
	
	public function rollBack()
	{
		$this->entityManager->getConnection()->rollBack();
	}
	
	public function flush()
	{
		$this->entityManager->flush();
	}
	/**
	 * Factory method to creates a business object of type $boClassname.
	 * This method only creates the business object. It does not persist it!
	 * 
	 * @param string $boClassname The business object's classname.
	 * 
	 * @return IBusinessObject The business object.
	 */
	public function create($boClassname)
	{
		$object =  new $boClassname;
		$this->initObject($object);
		return $object;
	}

	public function findAll($boClassname)
	{
		$objs = $this->entityManager->getRepository($boClassname)->findAll();
		foreach ($objs as $obj) {
			$this->initObject($obj);
		}		
		return $objs;
	}
	
	public function find($boClassname, $id)
	{
		$obj = $this->entityManager->find($boClassname, $id);
		if (is_null($obj)) {
			throw new ObjectNotFoundException();
		}
		$this->initObject($obj);
		return $obj;
	}
	
	public function save(IBusinessObject $object)
	{
		$dateTime = new DateTime();
		if ($object->getId() == null) {
			$object->setCreatedAt($dateTime);
			$this->entityManager->persist($object);
		}
		$object->setModifiedAt($dateTime);
		return $object;
	}

	public function delete(IBusinessObject $object)
	{
		$this->entityManager->remove($object);
	}
}