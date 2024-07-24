<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\DefinitionInterface;
use IfCastle\TypeDefinitions\FromEnv;
use IfCastle\TypeDefinitions\NativeSerialization\ArraySerializableInterface;
use IfCastle\TypeDefinitions\NativeSerialization\ArraySerializableValidatorInterface;

interface ParameterDescriptorInterface extends ArraySerializableInterface
{
    final public const string NAME = 'n';
    
    final public const string DEFINITION = 'd';
    
    final public const string DEFAULT = '=';
    
    final public const string ATTRIBUTES = 'a';
    
    public function getName(): string;
    
    public function getDefinition(): DefinitionInterface;
    
    public function getResolver(): callable|null;
    
    public function isDefaultValueAvailable(): bool;
    
    public function getDefaultValue(): mixed;
    
    public function getAttributes(): array;
    
    public function fromEnv(): ?FromEnv;
}