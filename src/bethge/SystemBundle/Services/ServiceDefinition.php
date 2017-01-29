<?php
namespace bethge\SystemBundle\Services;

use bethge\SystemBundle\Exceptions\BadRequestException;

class ServiceDefinition {
	
	private $versions; 
	private $services;
	private $namespaces; 
	
	private $serviceDef;
	private $version;
	private $namespace;
	private $service;
	
	public function __construct(array $serviceModel, $version, $namespace, $service)
	{
		$this->serviceDef = null;
		$this->versions = array_keys($serviceModel);
		if ($version) {
			if (!isset($serviceModel[$version])) {
				throw new BadRequestException('unknown version ' . $version);
			}
			$this->version = $version;
			$this->namespaces = array_keys($serviceModel[$version]);
			if ($namespace) {
				if (!isset($serviceModel[$version][$namespace])) {
					throw new BadRequestException('unknown namespace ' . $namespace);
				}
				$this->namespace = $namespace;
				$this->services = array_keys($serviceModel[$version][$namespace]);
				if ($service) {
					if (!isset($serviceModel[$version][$namespace][$service])) {
						throw new BadRequestException('unknown service ' . $service);
					}
					$this->service = $service;
					$this->serviceDef = $serviceModel[$version][$namespace][$service];
				}	
			}
		}
	}
	
	public function getShortNamespace()
	{
		return $this->namespace;
	}
	
	public function getShortType()
	{
		return $this->service;
	}
	
	public function getVersion()
	{
		return $this->version;
	}
	
	public function getCrudManagerClassname()
	{
		return $this->serviceDef['crudManager'];
	}
	
	public function getServiceClassname()
	{
		return $this->serviceDef['service'];
	}
	
	public function getBusinessObjectClassname()
	{
		return $this->serviceDef['businessObject'];
	}
	
	public function getVersions()
	{
		return $this->versions;
	}
	
	public function getServices()
	{
		return $this->services;
	}
	
	public function getNamespaces()
	{
		return $this->namespaces;
	}
}