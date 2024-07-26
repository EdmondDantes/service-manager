<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\DependencyInterface;
use IfCastle\ServiceManager\Exceptions\MethodNotFound;
use IfCastle\TypeDefinitions\FunctionDescriptorInterface;

interface ServiceDescriptorInterface extends DependencyInterface, DescriptorInterface
{
    public function getServiceName(): string;
    
    public function getClassName(): string;
    
    public function isServiceActive(): bool;
    
    public function getServiceConfig(): array;
    
    /**
     * @return FunctionDescriptorInterface[]
     */
    public function getServiceMethods(): array;
    
    /**
     * Returns method descriptor if exists or NULL.
     *
     *
     */
    public function findServiceMethod(string $method): ?FunctionDescriptorInterface;
    
    /**
     * Returns method descriptor for service
     *
     *
     * @throws  MethodNotFound
     */
    public function getServiceMethod(string $method): FunctionDescriptorInterface;
}