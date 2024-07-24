<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\DefinitionInterface;

readonly class MethodDescriptor     implements MethodDescriptorInterface
{
    public function __construct(
        public ServiceDescriptorInterface $service,
        public string $method,
        public array $parameters,
        public DefinitionInterface $return,
        public array $errors,
        public array $accessRoles   = [],
        public array $attributes    = [],
        public string $description  = ''
    )
    {
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
    public function getServiceClass(): string
    {
        return $this->service->getServiceName();
    }
    
    #[\Override]
    public function getServiceDescriptor(): ServiceDescriptorInterface
    {
        return $this->service;
    }
    
    #[\Override]
    public function getMethod(): string
    {
        return $this->method;
    }
    
    #[\Override]
    public function getDescription(): string
    {
        return $this->description;
    }
    
    #[\Override]
    public function getParameters(): array
    {
        return $this->parameters;
    }
    
    #[\Override]
    public function getReturn(): DefinitionInterface
    {
        return $this->return;
    }
    
    #[\Override]
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    #[\Override]
    public function getAccessRoles(): array
    {
        return $this->accessRoles;
    }
}