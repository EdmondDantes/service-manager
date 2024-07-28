<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\Binding;
use IfCastle\TypeDefinitions\NativeSerialization\AttributeNameInterface;
use IfCastle\TypeDefinitions\Reader\Exceptions\TypeUnresolved;
use IfCastle\TypeDefinitions\Reader\ReflectionFunctionReader;
use IfCastle\TypeDefinitions\Resolver\ResolverInterface;

class ServiceDescriptorByReflection extends ServiceDescriptor
{
    /**
     * @param object|string     $service
     * @param string            $serviceName
     * @param ResolverInterface $resolver
     * @param bool              $isActive
     * @param array             $config
     * @param bool              $useOnlyServiceMethods
     *
     * @throws TypeUnresolved
     * @throws \ReflectionException
     */
    public function __construct(
        object|string     $service,
        string            $serviceName,
        ResolverInterface $resolver,
        bool              $isActive = true,
        array             $config   = [],
        protected readonly bool $useOnlyServiceMethods = true
    )
    {
        $reflectionClass            = new \ReflectionClass($service);
        
        parent::__construct($serviceName, $reflectionClass->getName(), isActive: $isActive, config: $config);
        
        $this->serviceName          = $serviceName;
        $this->attributes           = $this->buildAttributes($reflectionClass->getAttributes());
        $this->buildMethods($reflectionClass, $resolver);
        
        // calculate bindings
        foreach ($reflectionClass->getAttributes(Binding::class) as $binding) {
            $this->bindings        = array_merge($this->bindings, $binding->newInstance()->interfaces);
        }
    }

    /**
     * @throws TypeUnresolved
     */
    protected function buildMethods(\ReflectionClass $reflectionClass, ResolverInterface $resolver): void
    {
        $functionReader             = new ReflectionFunctionReader($resolver);
        
        foreach ($this->fetchClassMethods($reflectionClass) as $method) {
            $this->methods[$method->getName()] = $functionReader->extractMethodDescriptor($method, $method->getName());
        }
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

    protected function fetchClassMethods(\ReflectionClass $class, array $methodReflections = []): array
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
            if($this->useOnlyServiceMethods && empty($method->getAttributes(
                ServiceMethod::class, \ReflectionAttribute::IS_INSTANCEOF
            ))) {
                continue;
            }

            if(false === array_key_exists($method->getName(), $methodReflections)) {
                $methodReflections[$method->getName()] = $method;
            }
        }
        
        if($class->getParentClass() !== false) {
            $methodReflections      = $this->fetchClassMethods($class->getParentClass(), $methodReflections);
        }
        
        return $methodReflections;
    }
}