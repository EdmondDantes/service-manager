<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

/**
 * Extracts services configuration from a repository by scope.
 */
interface RepositoryReaderByScopeInterface
{
    /**
     * @return array<string, array>
     */
    public function getServicesConfigByTags(string ...$tags): array;
    
    public function findServiceConfigByTags(string $serviceName, string ...$tags): array|null;
}