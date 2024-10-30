<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

/**
 * Extracts services configuration from a repository by scope.
 */
interface RepositoryReaderByScopeInterface
{
    /**
     * @return array<string, array<mixed>>
     */
    public function getServicesConfigByTags(string ...$tags): array;

    /**
     * @return array<mixed>|null
     */
    public function findServiceConfigByTags(string $serviceName, string ...$tags): array|null;

    /**
     * @return array<string, array<mixed>>
     */
    public function findServicesConfigByTags(string ...$tags): array;

    /**
     * @return array<string, array<mixed>>
     */
    public function findServicesConfigByPackage(string $packageName): array;
}
