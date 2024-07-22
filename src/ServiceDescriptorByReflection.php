<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

class ServiceDescriptorByReflection implements ServiceDescriptorInterface
{
    private array|null $dependencies    = null;
    
    public function __construct(
        private readonly string $serviceName,
        private readonly string $className,
        private readonly bool   $isActive        = true,
        private readonly array  $config          = []
    ) {}
    
    public function getDependencyDescriptors(): array
    {
        if($this->dependencies !== null) {
            return $this->dependencies;
        }
        
        
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