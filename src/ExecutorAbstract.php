<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\ServiceManager\Exceptions\ServiceException;
use IfCastle\TypeDefinitions\TypeInternal;
use IfCastle\TypeDefinitions\Value\ValueContainerInterface;

abstract class ExecutorAbstract     implements ExecutorInterface
{
    /**
     * @throws ServiceException
     */
    public static function throwIfServiceNameInvalid(string $serviceName): void
    {
        if(!preg_match('/^[A-Z][A-Z\/0-9]+$/i', $serviceName)) {
            throw new ServiceException([
                'template'          => 'Invalid service name {serviceName}. Should be match with pattern /[A-Z][A-Z\/0-9]+/i',
                'serviceName'       => $serviceName
            ]);
        }
    }
 
    protected ServiceTracerInterface|null $tracer       = null;
    
    public function executeCommand(
        string|CommandDescriptorInterface $service,
        string                            $command      = null,
        array                             $parameters   = [],
        ExecutionContextInterface         $context      = null
    ): mixed
    {
        if($service instanceof CommandDescriptorInterface) {
            $command                = $service->getMethodName();
            $parameters             = $service->getParameters();
            $service                = $service->getServiceName();
        }
        
        self::throwIfServiceNameInvalid($service);
        
        [$serviceObject, $serviceDescriptor] = $this->resolveService($service);
        
        $this->checkAccess($serviceObject, $serviceDescriptor, $service, $command);
        
        $methodDescriptor           = $serviceDescriptor->getServiceMethod($command);

        if(($job = $this->tryRunningAsJob($serviceDescriptor, $methodDescriptor, $service, $command, $parameters)) !== null) {
            return $job;
        }
        
        $parameters                 = $this->normalizeParameters($parameters, $methodDescriptor);
        
        return $this->runCommand($serviceObject, $serviceDescriptor, $command, $parameters, $service);
    }
    
    /**
     * @throws ServiceException
     */
    protected function normalizeParameters(array $parameters, MethodDescriptorInterface $methodDescriptor): array
    {
        $normalized                 = [];
        
        foreach ($methodDescriptor->getParameters() as $parameter)
        {
            $definition             = $parameter->getDefinition();
            $parameterName          = $definition->getName();
            $isParameterExists      = array_key_exists($parameterName, $parameters);
            
            if(false === $isParameterExists && $parameter->fromEnv() !== null) {
                
                $value              = $this->extractParameterFromEnv($parameter);
                
                if($value !== null) {
                    $normalized[$parameterName] = $value;
                    continue;
                }
            }
            
            if(false === $parameter->isDefaultValueAvailable() && false === $isParameterExists) {
                throw new ServiceException([
                   'template'      => 'Parameter "{parameter}" required by {service}->{method}',
                   'parameter'     => $parameterName,
                   'service'       => $methodDescriptor->getServiceClass(),
                   'method'        => $methodDescriptor->getMethod()
                ]);
            }
            
            if(false === $isParameterExists) {
                
                if($parameter->isDefaultValueAvailable()) {
                    $normalized[$parameterName] = $parameter->getDefaultValue();
                    continue;
                }
                
                if($definition->isNullable()) {
                    $normalized[$parameterName] = null;
                    continue;
                }
                
                continue;
            }
            
            if($definition instanceof TypeInternal || $parameters[$parameterName] instanceof SerializableI) {
                $normalized[$parameterName] = $parameters[$parameterName];
            } else {
                $normalized[$parameterName] = $definition->decode($parameters[$parameterName]);
            }
        }
        
        return $normalized;
    }
    
    protected function extractParameterFromEnv(ParameterDescriptor $parameter): mixed
    {
        $fromEnv            = $parameter->fromEnv();
        $env                = $this->systemEnvironment;
        $key                = $fromEnv->key ?? $parameter->getName();
        
        if($fromEnv->fromRequest) {
            $env            = $this->systemEnvironment->getRequestEnvironment();
        }
        
        if($env === null) {
            return null;
        }
        
        if($fromEnv->factory !== null) {
            
            if(false === $env->hasDependency($fromEnv->factory)) {
                return null;
            }
            
            $env            = $env->getDependency($fromEnv->factory);
        }
        
        if(false === $fromEnv->asDependency && $env instanceof EnvironmentI) {
            return $env->get($key);
        }
        
        if($env instanceof LocatorI && $env->hasDependency($key)) {
            return $env->getDependency($key);
        }
        
        return null;
    }
    
    protected function runCommand(
        object                     $service,
        ServiceDescriptorInterface $serviceDescriptor,
        string                     $method,
        array                      $parameters,
        string                     $serviceName
    ): mixed
    {
        $this->tracer?->startServiceCall($serviceName, $serviceDescriptor, $method, $parameters);
        
        try {
            
            $result                 = call_user_func([$service, $method], ...array_values($parameters));
            
            $this->tracer?->recordResult($result);
            
            return $result;
            
        } catch (\Throwable $throwable) {
            $this->tracer?->recordException($throwable);
            throw $throwable;
        } finally {
            $this->tracer?->end();
        }
    }
    
    protected function checkAccess(
        object                     $serviceObject,
        ServiceDescriptorInterface $serviceDescriptor,
        string                     $service,
        string                     $command
    ): void {}
    
    protected function tryRunningAsJob(
        ServiceDescriptorInterface $serviceDescriptor,
        MethodDescriptorInterface  $methodDescriptor,
        string                     $service,
        string                     $command,
        array                      $parameters
    ): ValueContainerInterface|null
    {
        return null;
    }
    
    abstract protected function resolveService(string $serviceName): array;
}