<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

interface RepositoryReaderInterface
{
    /**
     * @return array<string, array<mixed>>
     */
    public function getServicesConfig(): array;

    /**
     * @return array<string, array<mixed>>
     */
    public function findServiceConfig(string $serviceName): array|null;
}
