<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\Value\ValueContainerInterface;

interface TaskRunnerInterface
{
    public function tryRunningAsTask(
        ServiceDescriptorInterface $serviceDescriptor,
        MethodDescriptorInterface  $methodDescriptor,
        string                     $service,
        string                     $command,
        array                      $parameters
    ): ValueContainerInterface|null;
}