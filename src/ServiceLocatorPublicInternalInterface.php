<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface ServiceLocatorPublicInternalInterface extends ServiceLocatorInterface
{
    public function getPublicServiceList(bool $shouldUpdate = false): array;
    
    public function findPublicService(string $serviceName): ?object;
    
    public function getPublicService(string $serviceName): object;
}