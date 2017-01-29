<?php
namespace bethge\SystemBundle\Entity;

use bethge\SystemBundle\Interfaces\IBusinessObject;
use \DateTime;

/**
 * Node
 */
class Node implements IBusinessObject
{
    /**
     * @var integer
     */
    private $id;
 
    private $name;
    private $createdAt;
    private $modifiedAt;
    
    // private properties injected via reflection
    private $repository;

    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public static function getClassname()
    {
    	return get_class();
    }
    
    public function getTestRepositoryResponse()
    {
    	return $this->repository->test();
    }
    
    public function getName()
    {
    	return $this->name;
    }
    
    public function setName($value)
    {
    	$this->name = $value;
    }
    
    public function getCreatedAt()
    {
    	return $this->createdAt;
    }
    
    public function setCreatedAt(DateTime $value)
    {
    	$this->createdAt = $value;
    }
    
    public function getModifiedAt()
    {
    	return $this->modifiedAt;
    }
    
    public function setModifiedAt(DateTime $value)
    {
    	$this->modifiedAt = $value;
    }
    
}

