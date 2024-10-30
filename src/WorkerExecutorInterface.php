<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface WorkerExecutorInterface
{
    public function executeCommandInWorker(
        string|CommandDescriptorInterface $service,
        ?string                            $command      = null,
        array                             $parameters   = [],
        ?ExecutionContextInterface         $context      = null
    ): mixed;

    public function executeCommandInWorkerAsync(
        string|CommandDescriptorInterface $service,
        ?string                            $command      = null,
        array                             $parameters   = [],
        ?ExecutionContextInterface         $context      = null
    ): int;
}
