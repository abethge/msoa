<?php
namespace bethge\SystemBundle\Services;

use bethge\SystemBundle\Interfaces\IBusinessObject;
use bethge\SystemBundle\Interfaces\IPersistenceManager;

use bethge\SystemBundle\Exceptions\WrongParameterException;

use \Exception;

/**
 * A service api for the node domain. 
 * Has no knowledge about the framework and persistenc - e.g. symfony.
 * So it will work as php api as well as as rpc api.
 * 
 * @author bethge
 *
 */
class BasicCrudService implements IService
{
	/** @var IPersistanceManager $crudManager */
	protected $crudManager;
	/** @var string $boClassname */
	protected $boClassname;
	
	/**
	 * Creates the basic service.
	 * 
	 * @param IPersistenceManager $crudManager
	 * @param string              $boClassname
	 * 
	 * @return void
	 */
	public function __construct(IPersistenceManager $crudManager, $boClassname)
	{
		$this->crudManager = $crudManager;
		$this->boClassname = $boClassname;
	}
	
	/**
	 * Delegates the creation of a business object to the appropriate CRUD manager's
	 * factory method which e.g. injects the repository.
	 * 
	 * This method does not persist the created object.
	 * 
	 * Call this method via your frontend controller to receive an object blueprint.
	 * Or call it directly (e.g. for internal use or for testing). Don't forget to 
	 * call the manager's flush method afterwards to really persist the object.
	 * 
	 * @return IBusinessObject The newly created business object.
	 */
	public function create()
	{
		return $this->crudManager->create($this->boClassname);
	}
	
	/**
	 * Delegates to the appropriate CRUD manager to prepare the given business object 
	 * for persisting. 
	 * 
	 * This method is intended for calling directly (e.g. for internal use or 
	 * for testing). Don't forget to call the manager's flush method afterward
	 * to really persist the object. 
	 * 
	 * @param IBusinessObject $object The business object to save.
	 * 
	 * @return IBusinessObject
	 */
	public function save(IBusinessObject $object)
	{
		$obj = null;
		// begin transaction
		$this->crudManager->beginTransaction();
		try {
			$obj = $this->crudManager->save($object);
			$this->crudManager->flush();
			// end transaction
			$this->crudManager->commit();
		} catch (Exception $e) {
			// roll back transaction
			$this->crudManager->rollBack();
			throw $e;
		}
		return $obj;
	}
	
	public function update(array $attributes)
	{
		$obj = null;
		// begin transaction
		$this->crudManager->beginTransaction();
		try {
			if (!isset($attributes['id']) || is_null($attributes['id'])) { 
				// create
				$obj = $this->create();
			} else { 
				// find
				$obj = $this->find($attributes['id']);
			}
			// update
			foreach ($attributes as $attrName => $attrValue) {
				if ($attrName != 'id') {
					$setter = 'set' . ucfirst($attrName);
					if (method_exists($obj, $setter)) {
						$obj->$setter($attrValue);
					} else {
						throw new WrongParameterException();
					}
				}
			}
			// save
			$obj = $this->crudManager->save($obj);
			$this->crudManager->flush();
			// end transaction
			$this->crudManager->commit();
		} catch (Exception $e) {
			// roll back transaction
			$this->crudManager->rollBack();
			throw $e;
		}
		return $obj;
	}
	
	public function findAll()
	{
		return $this->crudManager->findAll($this->boClassname);
	}
	
	public function find($id)
	{
		return $this->crudManager->find($this->boClassname, $id);
	}
	
	public function delete($id)
	{
		// begin transaction
		$this->crudManager->beginTransaction();
		try {
			$this->crudManager->delete(
				$this->crudManager->find($this->boClassname, $id)
			);
			$this->crudManager->flush();
			// end transaction
			$this->crudManager->commit();
		} catch (Exception $e) {
			// roll back transaction
			$this->crudManager->rollBack();
			throw $e;
		}
	}
}