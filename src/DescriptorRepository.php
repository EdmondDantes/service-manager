<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\ServiceManager\Exceptions\ServiceConfigException;
use IfCastle\ServiceManager\Exceptions\ServiceNotFound;
use IfCastle\ServiceManager\RepositoryStorages\RepositoryReaderInterface;
use IfCastle\TypeDefinitions\Resolver\ResolverInterface;

class DescriptorRepository implements DescriptorRepositoryInterface
{
    protected array|null $serviceDescriptors = null;
    
    public function __construct(protected readonly RepositoryReaderInterface $repositoryReader, protected readonly ResolverInterface $resolver) {}
    
    public function findServiceClass(string $serviceName): string|null
    {
        $this->load();
        
        return $this->serviceDescriptors[$serviceName]?->getClassName();
    }
    
    public function getServiceDescriptorList(bool $onlyActive = true): array
    {
        $this->load();
        
        return $this->serviceDescriptors;
    }
    
    public function findServiceDescriptor(string $serviceName): ServiceDescriptorInterface|null
    {
        $this->load();
        
        return $this->serviceDescriptors[$serviceName] ?? null;
    }
    
    public function getServiceDescriptor(string $serviceName): ServiceDescriptorInterface
    {
        $this->load();
        
        return $this->serviceDescriptors[$serviceName] ?? throw new ServiceNotFound($serviceName);
    }
    
    /**
     * @throws ServiceConfigException
     */
    protected function load(): void
    {
        if($this->serviceDescriptors !== null) {
            return;
        }
        
        $serviceDescriptors         = [];
        
        foreach($this->repositoryReader->getServicesConfig() as $serviceName => $serviceConfig) {
            
            if(false === array_key_exists('class', $serviceConfig)) {
                throw new ServiceConfigException([
                    'template'      => 'Service {serviceName} has no class defined',
                    'serviceName'   => $serviceName,
                ]);
            }
            
            if(empty($serviceConfig['isActive'])) {
                continue;
            }
            
            $serviceDescriptors[$serviceName] = new ServiceDescriptorByReflection(
                $serviceName,
                $serviceConfig['class'],
                $this->resolver,
                true,
                $serviceConfig
            );
        }
        
        $this->serviceDescriptors = $serviceDescriptors;
    }
}