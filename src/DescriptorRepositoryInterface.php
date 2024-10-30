<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface DescriptorRepositoryInterface
{
    public function findServiceClass(string $serviceName): string|null;

    /**
     *
     * @return ServiceDescriptorInterface[]
     */
    public function getServiceDescriptorList(bool $onlyActive = true): array;

    public function findServiceDescriptor(string $serviceName): ServiceDescriptorInterface|null;

    public function getServiceDescriptor(string $serviceName): ServiceDescriptorInterface;
}
