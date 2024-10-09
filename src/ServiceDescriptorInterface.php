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
    
    /**
     * Returns a list of interfaces that are used for binding this service.
     *
     * @return string[]
     */
    public function getBindings(): array;
    
    /**
     * The method returns a list of Scopes in which the service is visible.
     *
     * @return string[]
     */
    public function getIncludeScopes(): array;
    
    /**
     * The method returns a list of Scopes in which the service is not visible.
     *
     * @return string[]
     */
    public function getExcludeScopes(): array;
    
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