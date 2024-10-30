<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\FunctionDescriptorInterface;
use IfCastle\TypeDefinitions\Value\ValueContainerInterface;

interface TaskRunnerInterface
{
    /**
     * @param ServiceDescriptorInterface  $serviceDescriptor
     * @param FunctionDescriptorInterface $methodDescriptor
     * @param string                      $service
     * @param string                      $command
     * @param array<string, mixed>        $parameters
     *
     * @return ValueContainerInterface|null
     */
    public function tryRunningAsTask(
        ServiceDescriptorInterface $serviceDescriptor,
        FunctionDescriptorInterface $methodDescriptor,
        string                     $service,
        string                     $command,
        array                      $parameters
    ): ValueContainerInterface|null;
}
