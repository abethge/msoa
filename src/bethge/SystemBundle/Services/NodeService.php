<?php
namespace bethge\SystemBundle\Services;

use bethge\SystemBundle\Interfaces\IBusinessObject;
use bethge\SystemBundle\Interfaces\IPersistenceManager;

/**
 * A service api for the node domain. 
 * Has no knowledge about the framework and persistenc - e.g. symfony.
 * So it will work as php api as well as as rpc api.
 * 
 * @author bethge
 *
 */
class NodeService extends BasicCrudService
{	
	public function __construct(IPersistenceManager $crudManager, $boClassname)
	{
		parent::__construct($crudManager, $boClassname);
	}
	
	public function createBulk($prefix, $number)
	{
		$ret = array();
		// begin transaction
		$this->crudManager->beginTransaction();
		try {
			for ($i = 0; $i < $number; $i++) {
				$obj = $this->create();
				$obj->setName($prefix . ' ' . (string)($i + 1));
				$this->save($obj);
				$ret[] = $obj;
			}
			$this->crudManager->flush();
			// end transaction
			$this->crudManager->commit();
		} catch (Exception $e) {
			// roll back transaction
			$this->crudManager->rollBack();
			throw $e;
		}
	
		return $ret;
	}
}