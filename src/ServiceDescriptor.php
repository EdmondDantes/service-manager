<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\ServiceManager\Exceptions\MethodNotFound;

class ServiceDescriptor             implements ServiceDescriptorInterface
{
    public function __construct(
        protected string $serviceName,
        protected string $className,
        protected array  $methods         = [],
        protected bool   $isActive        = true,
        protected array  $config          = [],
        protected array  $dependencies    = [],
        protected array  $attributes      = []
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
    
    #[\Override]
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    #[\Override]
    public function findAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }
    
    #[\Override]
    public function getServiceMethods(): array
    {
        return $this->methods;
    }
    
    #[\Override]
    public function findServiceMethod(string $method): ?MethodDescriptorInterface
    {
        return $this->methods[$method] ?? null;
    }
    
    #[\Override]
    public function getServiceMethod(string $method): MethodDescriptorInterface
    {
        return $this->methods[$method] ?? throw new MethodNotFound($this->serviceName, $method);
    }
}