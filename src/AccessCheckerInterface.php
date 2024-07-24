<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface AccessCheckerInterface
{
    public function checkAccess(
        object                     $serviceObject,
        ServiceDescriptorInterface $serviceDescriptor,
        MethodDescriptorInterface  $methodDescriptor,
        string                     $service,
        string                     $command
    ): void;
}