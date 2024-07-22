<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface ServiceManagerInterface
{
    public function getServiceList(bool $shouldUpdate = false): array;
    
    public function findServiceClass(string $serviceName): ?string;
    
    public function findService(string $serviceName): ?object;
    
    public function getService(string $serviceName): object;
    
    public function findServiceDescriptor(string $serviceName): ?ServiceDescriptorInterface;
    public function getServiceDescriptor(string $serviceName): ServiceDescriptorInterface;
}