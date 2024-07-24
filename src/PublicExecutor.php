<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\ContainerInterface;

abstract class PublicExecutor extends ExecutorAbstract
{
    public function __construct(
        private readonly ServiceLocatorInterface $serviceLocator,
        ContainerInterface $systemEnvironment = null,
        AccessCheckerInterface $accessChecker = null,
        TaskRunnerInterface $taskRunner = null,
        ServiceTracerInterface $tracer = null
    )
    {
        $this->systemEnvironment    = $systemEnvironment;
        $this->accessChecker        = $accessChecker;
        $this->taskRunner           = $taskRunner;
        $this->tracer               = $tracer;
    }
    
    protected function resolveService(string $serviceName): array
    {
        $service                    = $this->serviceLocator->findService($serviceName);
        
        if($service === null) {
            return [null, null];
        }
        
        return [$service, $this->serviceLocator->getServiceDescriptor($serviceName)];
    }
}