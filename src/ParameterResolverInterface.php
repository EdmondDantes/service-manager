<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\DefinitionInterface;
use IfCastle\TypeDefinitions\FunctionDescriptorInterface;

interface ParameterResolverInterface
{
    public function resolveParameters(DefinitionInterface $parameter, FunctionDescriptorInterface $methodDescriptor, array $rawParameters = []): mixed;
}
