<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

interface RepositoryWriterInterface extends RepositoryReaderInterface
{
    public function addServiceConfig(string     $serviceName,
                                     array      $serviceConfig,
                                     bool       $isActive = true,
                                     array|null $includeTags = null,
                                     array|null $excludeTags = null
    ): void;
    
    public function removeServiceConfig(string $serviceName): void;
    
    public function updateServiceConfig(string $serviceName, array $serviceConfig, array|null $includeTags = null, array|null $excludeTags = null): void;
    
    public function activateService(string $serviceName): void;
    
    public function deactivateService(string $serviceName): void;
    
    public function changeServiceTags(string $serviceName, array|null $includeTags = null, array|null $excludeTags = null): void;
    
    public function saveRepository(): void;
}