<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\Resolver\ResolverInterface;

interface ServiceDescriptorBuilderInterface
{
    /**
     * @param object|string     $service
     * @param string            $serviceName
     * @param ResolverInterface $resolver
     * @param bool              $isActive
     * @param array<string, mixed> $config
     * @param bool              $useOnlyServiceMethods
     * @param bool              $bindWithFirstInterface
     * @param bool              $bindWithAllInterfaces
     *
     * @return ServiceDescriptorInterface
     */
    public function buildServiceDescriptor(
        object|string     $service,
        string            $serviceName,
        ResolverInterface $resolver,
        bool              $isActive     = true,
        array             $config       = [],
        bool $useOnlyServiceMethods     = true,
        bool $bindWithFirstInterface    = false,
        bool $bindWithAllInterfaces     = false
    ): ServiceDescriptorInterface;
}
