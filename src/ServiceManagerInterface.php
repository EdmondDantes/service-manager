<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface ServiceManagerInterface
{
    public function installService(ServiceDescriptorInterface $serviceDescriptor): void;
    
    public function uninstallService(string $serviceName): void;
    
    public function activateService(string $serviceName): void;
    
    public function deactivateService(string $serviceName): void;
}