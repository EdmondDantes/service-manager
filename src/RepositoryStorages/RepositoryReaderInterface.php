<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

interface RepositoryReaderInterface
{
    /**
     * @return array<string, array>
     */
    public function getServicesConfig(): array;
    
    public function findServiceConfig(string $serviceName): array|null;
}