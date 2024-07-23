<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

final readonly class InternalExecutor extends ExecutorAbstract
{
    public function __construct(private ServiceLocatorInterface $publicLocator, private ServiceLocatorInterface $internalLocator) {}
    
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