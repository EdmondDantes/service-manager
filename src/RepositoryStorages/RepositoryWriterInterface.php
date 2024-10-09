<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

interface RepositoryWriterInterface extends RepositoryReaderInterface
{
    public function addServiceConfig(string $serviceName, array $serviceConfig, array $scopes = []): void;
    
    public function removeServiceConfig(string $serviceName): void;
    
    public function updateServiceConfig(string $serviceName, array $serviceConfig, array $scopes = []): void;
    
    public function activateService(string $serviceName): void;
    
    public function deactivateService(string $serviceName): void;
    
    public function changeServiceScope(string $serviceName, array $scopes): void;
}