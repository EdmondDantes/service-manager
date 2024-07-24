<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\ServiceManager\Exceptions\ServiceException;
use IfCastle\TypeDefinitions\DefinitionInterface;
use IfCastle\TypeDefinitions\DefinitionMutableInterface;
use IfCastle\TypeDefinitions\Error;
use IfCastle\TypeDefinitions\FromEnv;
use IfCastle\TypeDefinitions\NativeSerialization\AttributeNameInterface;
use IfCastle\TypeDefinitions\ReturnType;
use IfCastle\TypeDefinitions\Type;
use IfCastle\TypeDefinitions\TypeInternal;
use IfCastle\TypeDefinitions\TypeMixed;
use IfCastle\TypeDefinitions\TypeOneOf;
use IfCastle\TypeDefinitions\Value\ValueBoolean;
use IfCastle\TypeDefinitions\Value\ValueFloat;
use IfCastle\TypeDefinitions\Value\ValueNumber;
use IfCastle\TypeDefinitions\Value\ValueString;

class ServiceDescriptorByReflection extends ServiceDescriptor
{
    /**
     * @var \ReflectionMethod[]
     */
    protected array $methodReflections  = [];
    
    /**
     * @param object|string $service
     * @param string        $serviceName
     *
     * @throws ServiceException
     * @throws \ReflectionException
     */
    public function __construct(object|string $service, string $serviceName)
    {
        $reflectionClass            = new \ReflectionClass($service);
        $this->serviceName          = $serviceName;
        // Service attributes
        $this->attributes           = $this->buildAttributes($reflectionClass->getAttributes());

        try {
            $this->searchClassMethods($reflectionClass);
            
            foreach ($this->methodReflections as $reflection) {
                $this->handleMethod($reflection, $reflectionClass);
            }
            
        } finally {
            $this->methodReflections = [];
        }
    }

    /**
     *
     * @throws ServiceException
     */
    protected function handleMethod(\ReflectionMethod $method, \ReflectionClass $class): void
    {
        $this->methods[$method->getName()] = new MethodDescriptor(
            $this,
            $method->getName(),
            $this->buildParameters($method),
            $this->buildReturn($method),
            $this->buildErrors($method),
            $this->buildAccessRoles($method, $class),
            $this->buildAttributes($method->getAttributes()),
            $this->buildDocComment($method)
        );
    }

    /**
     * @throws ServiceException
     */
    protected function buildReturn(\ReflectionMethod $method): DefinitionInterface
    {
        $returnType                 = $method->getAttributes(ReturnType::class, \ReflectionAttribute::IS_INSTANCEOF);
        $returnType                 = $returnType !== [] ? $returnType[0]->newInstance() : null;

        if($returnType instanceof ReturnType) {
            return $returnType->definition;
        }

        $returnType                 = $method->getReturnType();

        if($returnType instanceof \ReflectionUnionType) {
            
            $enum                   = new TypeOneOf('returnType', false, $returnType->allowsNull());
            
            foreach ($returnType->getTypes() as $type) {
                $enum->describeCase($this->resolveDefinitionByType($type, $method));
            }
            
            return $enum->asImmutable();
        }
        
        return $this->resolveDefinitionByType($returnType, $method);
    }
    
    /**
     *
     * @throws ServiceException
     */
    protected function resolveDefinitionByType(\ReflectionType $type, \ReflectionMethod $method): DefinitionInterface
    {
        if($type instanceof \ReflectionIntersectionType) {
            throw new ServiceException([
               'template'           => 'Intersection type are not allowed for {service}::{method}',
               'service'            => $this->serviceName,
               'method'             => $method->getName()
           ]);
        }
        
        if($type instanceof \ReflectionNamedType) {
            $typeClass              = $type->getName();
        } elseif ($type->isBuiltin()) {
            $typeClass              = match ($type->getName()) {
                'string'            => ValueString::class,
                'int'               => ValueNumber::class,
                'bool'              => ValueBoolean::class,
                'float'             => ValueFloat::class,

                'array'             => throw new ServiceException([
                    'template'      => 'Type {type} is not allowed for {parameter} by {service}->{method}. Use attributes Type or ReturnType.',
                    'parameter'     => $type->getName(),
                    'service'       => $this->serviceName,
                    'type'          => $type->getName(),
                    'method'        => $method->getName()
                ])
            };
        } else {
            throw new ServiceException([
                'template'          => 'Type are not allowed for {service}::{method}',
                'service'           => $this->serviceName,
                'method'            => $method->getName()
            ]);
        }

        if($typeClass === 'array') {
            throw new ServiceException([
                'template'      => 'Type {type} is not allowed for {parameter} by {service}->{method}. Use attributes Type or ReturnType.',
                'parameter'     => $type->getName(),
                'service'       => $this->serviceName,
                'type'          => $type->getName(),
                'method'        => $method->getName()
            ]);
        }

        if(false === is_callable($typeClass.'::definition')) {
            return (new TypeInternal('type', $typeClass))
                ->setIsRequired(false)
                ->setIsNullable($type->allowsNull())
                ->asImmutable();
        }

        $definition             = clone call_user_func($typeClass.'::definition');

        if($definition instanceof DefinitionMutableInterface) {
            return $definition->setName('type')
                    ->setIsRequired(false)
                    ->setIsNullable($type->allowsNull())
                    ->asImmutable();
        }

        throw new ServiceException([
            'template'      => 'Type is not allowed {service}->{method}. Definition {definition} is wrong!',
            'service'       => $this->serviceName,
            'definition'    => get_debug_type($definition),
            'method'        => $method->getName()
        ]);
    }
    
    /**
     *
     * @throws ServiceException
     */
    protected function buildAccessRoles(\ReflectionMethod $method, \ReflectionClass $class): array
    {
        // Try to get class access
        $classAccess                = $class->getAttributes(Access::class, \ReflectionAttribute::IS_INSTANCEOF);

        $classAccess                = $classAccess !== [] ? $classAccess[0] : null;

        $access                     = $method->getAttributes(Access::class, \ReflectionAttribute::IS_INSTANCEOF);
        $access                     = $access !== [] ? $access[0] : null;

        if($classAccess !== null && $access !== null) {
            throw new ServiceException([
                'template'          => 'You cannot define #access at the method and class level at the same time for {service}->{method}',
                'service'           => $this->serviceName,
                'method'            => $method->getName()
            ]);
        }

        if($classAccess !== null && $access === null) {
            $access                 = $classAccess;
        }

        if(empty($access)) {
            return [];
        }

        $access                     = $access->newInstance();

        /* @var $access Access */
        $roles                      = [];

        foreach ($access->getRoles() as $role) {
            $roles[]                = $role;
        }

        return $roles;
    }

    protected function buildParameters(\ReflectionMethod $method): array
    {
        $parameters                 = [];

        foreach ($method->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = $this->buildParameterDescriptor($parameter, $method);
        }

        return $parameters;
    }

    /**
     *
     * @throws ServiceException
     */
    protected function buildParameterDescriptor(\ReflectionParameter $parameter, \ReflectionMethod $method): ParameterDescriptorInterface
    {
        $fromEnv                    = $parameter->getAttributes(FromEnv::class, \ReflectionAttribute::IS_INSTANCEOF);
        $fromEnv                    = $fromEnv !== [] ? $fromEnv[0]->newInstance() : null;

        $type                       = $parameter->getAttributes(Type::class, \ReflectionAttribute::IS_INSTANCEOF);
        $type                       = $type !== [] ? $type[0]->newInstance() : null;

        if($type instanceof Type) {

            // If the type is not defined, then we take the name of the parameter
            if($type->definition->getName() === '') {
                $type->definition->setName($parameter->getName());
            }

            $type->definition
                ->setIsRequired($parameter->isDefaultValueAvailable() === false)
                ->setIsNullable($parameter->allowsNull());

            return new ParameterDescriptor(
                $type->definition,
                $parameter->isDefaultValueAvailable(),
                $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                $fromEnv !== null ? [$fromEnv] : []
            );
        }

        $type                       = $parameter->getType();
        
        if($type === null) {
            throw new ServiceException([
                'template'          => 'Parameter {parameter} without type is not allowed for {service}->{method}',
                'parameter'         => $parameter->getName(),
                'service'           => $this->serviceName,
                'method'            => $method->getName()
            ]);
        }
        
        if($type instanceof \ReflectionNamedType === null) {
            throw new ServiceException([
                'template'          => 'Parameter {parameter} should be named for {service}->{method}',
                'parameter'         => $parameter->getName(),
                'service'           => $this->serviceName,
                'method'            => $method->getName()
            ]);
        }

        if($fromEnv !== null) {
            $class              = TypeMixed::class;
        } elseif($type->isBuiltin()) {
            
            $class              = match ($type->getName()) {
                'string'        => ValueString::class,
                'int'           => ValueNumber::class,
                'bool'          => ValueBoolean::class,
                'float'         => ValueFloat::class,
                'array'         => throw new ServiceException([
                    'template'      => 'Type {type} is not allowed for {parameter} by {service}->{method}',
                    'parameter'     => $parameter->getName(),
                    'service'       => $this->serviceName,
                    'type'          => $type->getName(),
                    'method'        => $method->getName()
                ])
            };
        } else {
            $class              = $type->getName();
        }
        
        if(is_callable($class.'::definition')) {
            $definition         = call_user_func($class.'::definition');
        } else {
            $definition         = new TypeInternal($parameter->getName(), $class);
        }
        
        if($definition instanceof DefinitionInterface) {
            
            $definition         = clone $definition;
            
            if($definition instanceof DefinitionMutableInterface) {
                
                $definition->setName($parameter->getName());
                $definition->setIsRequired($parameter->isDefaultValueAvailable() === false);
                $definition->setIsNullable($parameter->allowsNull());
                $definition->asImmutable();
    
                return new ParameterDescriptor(
                    $definition,
                    $parameter->isDefaultValueAvailable(),
                    $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                    $fromEnv !== null ? [$fromEnv] : []
                );
            }
        }

        throw new ServiceException([
            'template'      => 'Parameter {parameter} type is not allowed {service}->{method}',
            'parameter'     => $parameter->getName(),
            'service'       => $this->serviceName,
            'method'        => $method->getName()
        ]);
    }

    /**
     * @return DefinitionInterface[]
     * @throws ServiceException
     */
    public function buildErrors(\ReflectionMethod $method): array
    {
        $errors                     = [];

        foreach ($method->getAttributes(Error::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {

            $error                  = $attribute->newInstance();

            if($error instanceof Error) {

                $className          = $error->errorClassName;

                if(false === is_callable($className.'::definitionByAttribute')) {
                    throw new ServiceException([
                        'template'  => 'Incorrect exception class {class} in the {service}->{method}. '
                            .'Must support static method definitionByAttribute',
                        'class'     => $className,
                        'service'   => $this->serviceName,
                        'method'    => $method->getName()
                    ]);
                }

                $definition         = call_user_func($className.'::definitionByAttribute', $error);

                if($definition instanceof DefinitionInterface === false) {
                    throw new ServiceException([
                        'template'  => 'Incorrect definitionByAttribute returned type for {class} in the {service}->{method}. '
                            .'Must return type DefinitionI',
                        'class'     => $className,
                        'service'   => $this->serviceName,
                        'method'    => $method->getName()
                    ]);
                }

                $errors[]           = $definition;
            }
        }

        return $errors;
    }

    /**
     * Returns an array of attributes where key is className and value is attribute.
     * If some attributes have similar className value should be an array.
     *
     * @param    \ReflectionAttribute[]    $reflectionAttributes
     */
    protected function buildAttributes(array $reflectionAttributes): array
    {
        $result                     = [];

        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute              = $reflectionAttribute->newInstance();

            $key                    = $attribute instanceof AttributeNameInterface ? $attribute->getAttributeName() : $attribute::class;

            if(array_key_exists($key, $result)) {

                if(!is_array($result[$key])) {
                    $result[$key]   = [$result[$key]];
                }

                $result[$key][]     = $attribute;
            } else {
                $result[$key]       = $attribute;
            }
        }

        return $result;
    }

    public function buildDocComment(\ReflectionMethod $method): string
    {
        $docComment                 = $method->getDocComment();

        return $docComment !== false ? $docComment : '';
    }

    protected function searchClassMethods(\ReflectionClass $class): void
    {
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            
            if ($method->isStatic()) {
                continue;
            }
            
            if ($method->isAbstract()) {
                continue;
            }
            
            //
            // We use only those methods that are explicitly marked as service methods
            //
            if(empty($method->getAttributes(
                ServiceMethod::class, \ReflectionAttribute::IS_INSTANCEOF
            ))) {
                continue;
            }

            if(false === array_key_exists($method->getName(), $this->methodReflections)) {
                $this->methodReflections[$method->getName()] = $method;
            }
        }
        
        if($class->getParentClass() !== false) {
            $this->searchClassMethods($class->getParentClass());
        }
    }
}