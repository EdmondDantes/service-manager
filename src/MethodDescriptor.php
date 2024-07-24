<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\DefinitionInterface;
use IfCastle\TypeDefinitions\TypeFunction;

class MethodDescriptor              extends     TypeFunction
                                    implements  MethodDescriptorInterface
{
    public function __construct(
        protected ServiceDescriptorInterface $service,
        protected string $method,
        protected array $parameters,
        protected DefinitionInterface $return,
        protected array $errors,
        protected array $accessRoles   = [],
        array $attributes    = [],
        string $description  = ''
    )
    {
        parent::__construct(
            $method,
            '',
            '',
            false,
            true,
            false
        );
        
        $this->returnType           = $return;
        $this->properties           = $parameters;
        $this->attributes           = $attributes;
        $this->description          = $description;
    }
    
    #[\Override]
    public function getServiceDescriptor(): ServiceDescriptorInterface
    {
        return $this->service;
    }
    
    #[\Override]
    public function getErrors(): array
    {
        return $this->errors;
    }
}