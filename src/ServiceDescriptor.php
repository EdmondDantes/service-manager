<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\ConstructibleInterface;
use IfCastle\ServiceManager\Exceptions\MethodNotFound;
use IfCastle\TypeDefinitions\AttributesTrait;
use IfCastle\TypeDefinitions\FunctionDescriptorInterface;

class ServiceDescriptor             implements ServiceDescriptorInterface, ConstructibleInterface
{
    use AttributesTrait;
    
    public function __construct(
        protected string $serviceName,
        protected string $className,
        protected array  $methods         = [],
        protected bool   $isActive        = true,
        protected array  $config          = [],
        protected bool   $useConstructor  = true,
        protected array  $bindings        = [],
        protected array  $dependencies    = [],
        array  $attributes                = [],
        protected array  $includeScopes   = [],
        protected array  $excludeScopes   = []
    )
    {
        $this->attributes = $attributes;
    }
    
    #[\Override]
    public function useConstructor(): bool
    {
        return $this->useConstructor;
    }
    
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
    
    #[\Override]
    public function getDependencyName(): string
    {
        return $this->className;
    }
    
    public function isServiceActive(): bool
    {
        return $this->isActive;
    }
    
    #[\Override]
    public function getBindings(): array
    {
        return $this->bindings;
    }
    
    #[\Override]
    public function getIncludeScopes(): array
    {
        return $this->includeScopes;
    }
    
    #[\Override]
    public function getExcludeScopes(): array
    {
        return $this->excludeScopes;
    }
    
    public function getServiceConfig(): array
    {
        return $this->config;
    }
    
    #[\Override]
    public function getServiceMethods(): array
    {
        return $this->methods;
    }
    
    #[\Override]
    public function findServiceMethod(string $method): ?FunctionDescriptorInterface
    {
        return $this->methods[$method] ?? null;
    }
    
    #[\Override]
    public function getServiceMethod(string $method): FunctionDescriptorInterface
    {
        return $this->methods[$method] ?? throw new MethodNotFound($this->serviceName, $method);
    }
}