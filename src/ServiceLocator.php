<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\Container;
use IfCastle\DI\ContainerInterface;
use IfCastle\DI\DependencyInterface;
use IfCastle\DI\DescriptorInterface;
use IfCastle\DI\Resolver;
use IfCastle\ServiceManager\Exceptions\ServiceNotFound;

class ServiceLocator                extends Container
                                    implements ServiceLocatorInterface
{
    public function __construct(
        protected readonly DescriptorRepositoryInterface $descriptorRepository,
        ContainerInterface                               $parentContainer = null,
        bool                                             $isWeakParent = false
    )
    {
        parent::__construct(new Resolver, [], $parentContainer, $isWeakParent);
    }
    
    public function getServiceList(bool $shouldUpdate = false): array
    {
        $services                   = [];
        
        foreach ($this->descriptorRepository->getServiceDescriptorList() as $serviceDescriptor) {
            $services[$serviceDescriptor->getServiceName()] = $this->getService($serviceDescriptor->getServiceName());
        }
        
        return $services;
    }
    
    public function findServiceClass(string $serviceName): ?string
    {
        return $this->descriptorRepository->findServiceClass($serviceName);
    }
    
    public function findService(string $serviceName): ?object
    {
        return $this->tryLoadService($serviceName);
    }
    
    public function getService(string $serviceName): object
    {
        return $this->tryLoadService($serviceName) ?? throw new ServiceNotFound($serviceName);
    }
    
    public function findServiceDescriptor(string $serviceName): ?ServiceDescriptorInterface
    {
        return $this->descriptorRepository->findServiceDescriptor($serviceName);
    }
    
    public function getServiceDescriptor(string $serviceName): ServiceDescriptorInterface
    {
        return $this->descriptorRepository->getServiceDescriptor($serviceName);
    }
    
    public function getServiceDescriptorList(bool $onlyActive = true): array
    {
        return $this->descriptorRepository->getServiceDescriptorList($onlyActive);
    }
    
    public function tryLoadService(string $serviceName): object|null
    {
        if(array_key_exists($serviceName, $this->container)) {
            return parent::findDependency($serviceName);
        }
        
        $serviceDescriptor          = $this->descriptorRepository->findServiceDescriptor($serviceName);
        
        if(null === $serviceDescriptor) {
            return null;
        }
        
        $this->container[$serviceName] = $serviceDescriptor;
        
        return parent::findDependency($serviceName);
    }
    
    #[\Override]
    public function resolveDependency(
        DescriptorInterface|string $name,
        DependencyInterface        $forDependency = null
    ): mixed
    {
        if(false === array_key_exists($name, $this->container)) {
            $serviceDescriptor      = $this->descriptorRepository->findServiceDescriptor($name);
            
            if(null !== $serviceDescriptor) {
                $this->container[$name] = $serviceDescriptor;
            }
        }
        
        return parent::resolveDependency($name, $forDependency);
    }
    
    #[\Override]
    public function findDependency(
        DescriptorInterface|string $name,
        DependencyInterface        $forDependency = null
    ): mixed
    {
        if(false === array_key_exists($name, $this->container)) {
            $serviceDescriptor      = $this->descriptorRepository->findServiceDescriptor($name);
            
            if(null !== $serviceDescriptor) {
                $this->container[$name] = $serviceDescriptor;
            }
        }
        
        return parent::findDependency($name, $forDependency);
    }
    
    #[\Override]
    public function hasDependency(DescriptorInterface|string $key): bool
    {
        if(false === array_key_exists($key, $this->container) && null !== $this->descriptorRepository->findServiceDescriptor($key)) {
            return true;
        }
        
        return parent::hasDependency($key);
    }
    
    #[\Override]
    public function findKey(DescriptorInterface|string $key): string|null
    {
        if(false === array_key_exists($key, $this->container) && null !== $this->descriptorRepository->findServiceDescriptor($key)) {
            return $key;
        }
        
        return parent::findKey($key);
    }
}