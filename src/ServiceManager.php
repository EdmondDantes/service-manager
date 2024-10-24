<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\ServiceManager\Exceptions\ServiceException;
use IfCastle\ServiceManager\RepositoryStorages\RepositoryWriterInterface;

class ServiceManager                implements ServiceManagerInterface
{
    public function __construct(
        protected readonly RepositoryWriterInterface $repositoryWriter
    ) {}
    
    /**
     * @throws ServiceException
     */
    #[\Override]
    public function installService(ServiceDescriptorInterface $serviceDescriptor): void
    {
        $this->throwIfNotFound($serviceDescriptor->getServiceName());
        
        $serviceConfig              = $serviceDescriptor->getServiceConfig();
        $serviceConfig['class']     = $serviceDescriptor->getClassName();
        $serviceConfig['isActive']  = $serviceDescriptor->isServiceActive();
        $serviceConfig['tags']      = $serviceDescriptor->getIncludeTags();
        $serviceConfig['excludeTags'] = $serviceDescriptor->getExcludeTags();
        
        
        $this->repositoryWriter->addServiceConfig($serviceDescriptor->getServiceName(), $serviceConfig);
        $this->repositoryWriter->saveRepository();
    }
    
    /**
     * @throws ServiceException
     */
    #[\Override]
    public function uninstallService(string $serviceName): void
    {
        $this->throwIfNotFound($serviceName);
        $this->repositoryWriter->removeServiceConfig($serviceName);
        $this->repositoryWriter->saveRepository();
    }
    
    /**
     * @throws ServiceException
     */
    #[\Override]
    public function activateService(string $serviceName): void
    {
        $this->throwIfNotFound($serviceName);
        $this->repositoryWriter->activateService($serviceName);
        $this->repositoryWriter->saveRepository();
    }
    
    /**
     * @throws ServiceException
     */
    #[\Override]
    public function deactivateService(string $serviceName): void
    {
        $this->throwIfNotFound($serviceName);
        $this->repositoryWriter->deactivateService($serviceName);
        $this->repositoryWriter->saveRepository();
    }
    
    #[\Override]
    public function updateServiceConfig(ServiceDescriptorInterface $serviceDescriptor): void
    {
        $serviceConfig              = $serviceDescriptor->getServiceConfig();
        $serviceConfig['class']     = $serviceDescriptor->getClassName();
        $serviceConfig['isActive']  = $serviceDescriptor->isServiceActive();
        $serviceConfig['tags']    = $serviceDescriptor->getIncludeTags();
        $serviceConfig['excludeTags'] = $serviceDescriptor->getExcludeTags();
        
        $this->repositoryWriter->updateServiceConfig($serviceDescriptor->getServiceName(), $serviceConfig);
        $this->repositoryWriter->saveRepository();
    }
    
    /**
     * @throws ServiceException
     */
    protected function throwIfNotFound(string $serviceName): void
    {
        if ($this->repositoryWriter->findServiceConfig($serviceName) !== null) {
            throw new ServiceException([
                'template'          => 'Service {service} already exists',
                'service'           => $serviceName
           ]);
        }
    }
}