<?php
namespace bethge\SystemBundle\Persister;

use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;

class AbeEntityManagerDecorator extends EntityManagerDecorator
{
	public function __construct(EntityManagerInterface $wrapped)
	{
		parent::__construct($wrapped);
	}
	
	public function create()
	{
		throw new Exception('TEEEEEEST');
	}
}