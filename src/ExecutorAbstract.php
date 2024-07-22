<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

abstract readonly class ExecutorAbstract implements ExecutorInterface
{
    public function executeCommand(
        string|CommandDescriptorInterface $service,
        string                            $command      = null,
        array                             $parameters   = [],
        ExecutionContextInterface         $context      = null
    ): mixed
    {
        // TODO: Implement executeCommand() method.
    }
    
    abstract protected function resolveService(string $serviceName): ?object;
}