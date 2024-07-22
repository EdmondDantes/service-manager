<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

final readonly class InternalExecutor     extends ExecutorAbstract
{
    public function __construct(private ServiceLocatorInterface $serviceLocator) {}
    
    protected function resolveService(string $serviceName): ?object
    {
        // TODO: Implement resolveService() method.
    }
}