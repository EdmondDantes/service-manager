<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface ServiceLocatorInterface   extends DescriptorRepositoryInterface
{
    public function getServiceList(bool $shouldUpdate = false): array;
    
    public function findService(string $serviceName): ?object;
    
    public function getService(string $serviceName): object;
}