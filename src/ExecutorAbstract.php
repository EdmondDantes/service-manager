<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\AutoResolverInterface;
use IfCastle\DI\ContainerInterface;
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
    protected ContainerInterface|null $systemEnvironment = null;
    protected AccessCheckerInterface|null $accessChecker = null;
    protected TaskRunnerInterface|null $taskRunner = null;
    
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
        $methodDescriptor           = $serviceDescriptor->getServiceMethod($command);
        
        $this->accessChecker?->checkAccess($serviceObject, $serviceDescriptor, $methodDescriptor, $service, $command);

        if(($job = $this->taskRunner?->tryRunningAsTask($serviceDescriptor, $methodDescriptor, $service, $command, $parameters)) !== null) {
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
            
            if($parameter->getResolver() !== null) {
                $normalized[$parameterName] = $this->resolveParameter($parameter);
                continue;
            } elseif($parameter->fromEnv() !== null) {
                
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
                }
                
                if($definition->isNullable()) {
                    $normalized[$parameterName] = null;
                }
                
                continue;
            }
            
            if(is_object($parameters[$parameterName]) || $definition instanceof TypeInternal) {
                $normalized[$parameterName] = $parameters[$parameterName];
            } else {
                $normalized[$parameterName] = $definition->decode($parameters[$parameterName]);
            }
        }
        
        return $normalized;
    }
    
    protected function resolveParameter(ParameterDescriptorInterface $parameter): mixed
    {
        $resolver                   = $parameter->getResolver();
        
        if($resolver === null) {
            return null;
        }
        
        if($resolver instanceof AutoResolverInterface) {
            return $resolver->resolveDependencies($this->systemEnvironment);
        }
        
        return $resolver($parameter);
    }
    
    protected function extractParameterFromEnv(ParameterDescriptorInterface $parameter): mixed
    {
        $fromEnv            = $parameter->fromEnv();
        $env                = $this->systemEnvironment;
        $key                = $fromEnv->key ?? $parameter->getName();
        
        if($fromEnv->fromRequestEnv) {
            $env            = $this->getRequestEnv();
        }
        
        if($env === null) {
            return null;
        }
        
        if($fromEnv->factory !== null) {
            
            if(false === $env->hasDependency($fromEnv->factory)) {
                return null;
            }
            
            $env            = $env->findDependency($fromEnv->factory);
        }
        
        if($env instanceof ContainerInterface && $env->hasDependency($key)) {
            return $env->resolveDependency($key);
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
    
    protected function getRequestEnv(): ContainerInterface|null
    {
        return null;
    }
    
    abstract protected function resolveService(string $serviceName): array;
}