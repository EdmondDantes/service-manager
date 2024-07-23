<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

final readonly class PublicExecutor extends ExecutorAbstract
{
    public function __construct(private ServiceLocatorInterface $serviceLocator) {}
    
    protected function resolveService(string $serviceName): array
    {
        $service                    = $this->serviceLocator->findService($serviceName);
        
        if($service === null) {
            return [null, null];
        }
        
        return [$service, $this->serviceLocator->getServiceDescriptor($serviceName)];
    }
}