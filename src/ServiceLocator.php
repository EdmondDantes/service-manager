<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\Container;

class ServiceLocator                extends Container
                                    implements ServiceLocatorInterface
{
    public function getServiceList(bool $shouldUpdate = false): array
    {
        // TODO: Implement getServiceList() method.
    }
    
    public function findServiceClass(string $serviceName): ?string
    {
        // TODO: Implement findServiceClass() method.
    }
    
    public function findService(string $serviceName): ?object
    {
        // TODO: Implement findService() method.
    }
    
    public function getService(string $serviceName): object
    {
        // TODO: Implement getService() method.
    }
    
    public function findServiceDescriptor(string $serviceName): ?ServiceDescriptorInterface
    {
        // TODO: Implement findServiceDescriptor() method.
    }
    
    public function getServiceDescriptor(string $serviceName): ServiceDescriptorInterface
    {
        // TODO: Implement getServiceDescriptor() method.
    }
}