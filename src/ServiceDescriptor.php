<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

final readonly class ServiceDescriptor implements ServiceDescriptorInterface
{
    public function __construct(
        private string $serviceName,
        private string $className,
        private array  $methods         = [],
        private bool   $isActive        = true,
        private array  $config          = [],
        private array  $dependencies    = []
    ) {}
    
    public function getDependencyDescriptors(): array
    {
        return $this->dependencies;
    }
    
    public function getServiceName(): string
    {
        return $this->serviceName;
    }
    
    public function getClassName(): string
    {
        return $this->className;
    }
    
    public function isServiceActive(): bool
    {
        return $this->isActive;
    }
    
    public function getServiceConfig(): array
    {
        return $this->config;
    }
}