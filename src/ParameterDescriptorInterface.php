<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\DefinitionInterface;
use IfCastle\TypeDefinitions\FromEnv;

interface ParameterDescriptorInterface
{
    public function getName(): string;
    
    public function getDefinition(): DefinitionInterface;
    
    public function getResolver(): callable|null;
    
    public function isDefaultValueAvailable(): bool;
    
    public function getDefaultValue(): mixed;
    
    public function getAttributes(): array;
    
    public function fromEnv(): ?FromEnv;
}