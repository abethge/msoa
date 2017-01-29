<?php
namespace bethge\SystemBundle\Interfaces;

use bethge\SystemBundle\Interfaces\IBusinessObject;

interface IPersistenceManager
{
	public function create($boClassname);
	public function findAll($boClassname);
	public function find($boClassname, $id);
	public function save(IBusinessObject $object);
	public function delete(IBusinessObject $object);
	
	public function beginTransaction();
	public function commit();
	public function rollBack();
	public function flush();
}