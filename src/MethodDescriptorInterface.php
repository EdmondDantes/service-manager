<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\DefinitionInterface;

interface MethodDescriptorInterface extends DescriptorInterface
{
    public function getServiceClass(): string;
    
    /**
     * Returns service descriptor
     */
    public function getServiceDescriptor(): ServiceDescriptorInterface;
    
    public function getMethod(): string;
    
    public function getDescription(): string;
    
    /**
     * @return ParameterDescriptorInterface[]
     */
    public function getParameters(): array;
    
    public function getReturn(): DefinitionInterface;
    
    /**
     * @return DefinitionInterface[]
     */
    public function getErrors(): array;
    
    public function getAccessRoles(): array;
}