<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\Exceptions\UnSerializeException;
use IfCastle\TypeDefinitions\DefinitionInterface;
use IfCastle\TypeDefinitions\FromEnv;
use IfCastle\TypeDefinitions\NativeSerialization\ArraySerializableValidatorInterface;
use IfCastle\TypeDefinitions\NativeSerialization\ArrayTyped;

readonly class ParameterDescriptor    implements ParameterDescriptorInterface
{
    /**
     * @param    ArraySerializableValidatorInterface|null    $validator
     *
     * @throws UnSerializeException
     */
    #[\Override]
    public static function fromArray(array $array, ArraySerializableValidatorInterface $validator = null): static
    {
        return new self(
            ArrayTyped::unserialize($array[self::DEFINITION] ?? null, $validator),
            array_key_exists(self::DEFAULT, $array),
            $array[self::DEFAULT] ?? null,
            ArrayTyped::unserializeList($validator, ...$array[self::ATTRIBUTES] ?? [])
        );
    }
    
    public function __construct(
        public DefinitionInterface $definition,
        public bool        $isDefaultValueAvailable     = false,
        public mixed       $defaultValue                = null,
        public array       $attributes                  = []
    )
    {}
    
    #[\Override]
    public function getName(): string
    {
        return $this->definition->getName();
    }
    
    #[\Override]
    public function getDefinition(): DefinitionInterface
    {
        return $this->definition;
    }
    
    #[\Override]
    public function isDefaultValueAvailable(): bool
    {
        return $this->isDefaultValueAvailable;
    }
    
    #[\Override]
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }
    
    #[\Override]
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    #[\Override]
    public function getResolver(): callable|null
    {
        return null;
    }
    
    #[\Override]
    public function toArray(ArraySerializableValidatorInterface $validator = null): array
    {
        if($this->isDefaultValueAvailable) {
            return [
                self::DEFINITION        => ArrayTyped::serialize($this->definition, $validator),
                self::DEFAULT           => $this->defaultValue,
                self::ATTRIBUTES        => ArrayTyped::serializeList($validator, ...$this->attributes)
            ];
        }
        
        return [
            self::DEFINITION        => ArrayTyped::serialize($this->definition, $validator),
            self::ATTRIBUTES        => ArrayTyped::serializeList($validator, ...$this->attributes)
        ];
    }
    
    #[\Override]
    public function fromEnv(): ?FromEnv
    {
        foreach ($this->attributes as $attribute) {
            if($attribute instanceof FromEnv) {
                return $attribute;
            }
        }
        
        return null;
    }
}