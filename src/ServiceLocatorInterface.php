<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface ServiceLocatorInterface extends DescriptorRepositoryInterface
{
    /**
     * @return array<string, object>
     */
    public function getServiceList(bool $shouldUpdate = false): array;
    
    /**
     * @param string $serviceName
     *
     * @return object|null
     */
    public function findService(string $serviceName): ?object;

    public function getService(string $serviceName): object;
}
