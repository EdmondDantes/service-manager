<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

final readonly class PublicExecutor extends ExecutorAbstract
{
    public function __construct(private ServiceLocatorInterface $serviceLocator) {}
    
    protected function resolveService(string $serviceName): ?object
    {
        // TODO: Implement findService() method.
    }
}