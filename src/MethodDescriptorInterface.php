<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\DefinitionInterface;
use IfCastle\TypeDefinitions\FunctionDescriptorInterface;

interface MethodDescriptorInterface extends FunctionDescriptorInterface
{
    /**
     * Returns service descriptor
     */
    public function getServiceDescriptor(): ServiceDescriptorInterface;
    
    /**
     * @return DefinitionInterface[]
     */
    public function getErrors(): array;
}