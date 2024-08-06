<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\ContainerInterface;

final class InternalExecutor        extends ExecutorAbstract
{
    public const string PUBLIC_LOCATOR   = 'publicLocator';
    public const string INTERNAL_LOCATOR = 'internalLocator';
    public const string INTERNAL_EXECUTOR = 'internalExecutor';
    public const string PUBLIC_EXECUTOR = 'publicExecutor';
    
    public function __construct(
        private readonly ServiceLocatorInterface $publicLocator,
        private readonly ServiceLocatorInterface $internalLocator,
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
        $service                    = $this->publicLocator->findService($serviceName);
        
        if($service !== null) {
            return [$service, $this->publicLocator->getServiceDescriptor($serviceName)];
        }

        $service                    = $this->internalLocator->findService($serviceName);
        
        if($service === null) {
            return [null, null];
        }
        
        return [$service, $this->internalLocator->getServiceDescriptor($serviceName)];
    }
}