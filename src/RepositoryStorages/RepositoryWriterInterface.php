<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

interface RepositoryWriterInterface extends RepositoryReaderInterface, RepositoryReaderByTagsInterface
{
    /**
     * Adds a new service configuration to the repository.
     *
     * @param array<string, mixed> $serviceConfig
     * @param ?array<string>        $includeTags
     * @param ?array<string>        $excludeTags
     */
    public function addServiceConfig(
        string     $serviceName,
        array      $serviceConfig,
        bool       $isActive = true,
        array|null $includeTags = null,
        array|null $excludeTags = null
    ): void;

    public function removeServiceConfig(string $serviceName): void;

    /**
     * Updates the service configuration in the repository.
     *
     * @param array<string, mixed> $serviceConfig
     * @param ?array<string>        $includeTags
     * @param ?array<string>        $excludeTags
     */
    public function updateServiceConfig(string     $serviceName,
                                        array      $serviceConfig,
                                        array|null $includeTags = null,
                                        array|null $excludeTags = null
    ): void;

    public function activateService(string $serviceName): void;

    public function deactivateService(string $serviceName): void;

    /**
     * Changes the tags of a service.
     *
     * @param ?array<string> $includeTags
     * @param ?array<string> $excludeTags
     */
    public function changeServiceTags(string     $serviceName,
                                      array|null $includeTags = null,
                                      array|null $excludeTags = null
    ): void;

    public function saveRepository(): void;
}
