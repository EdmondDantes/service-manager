<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface WorkerExecutorInterface
{
    /**
     * @param array<string, mixed>               $parameters
     *
     */
    public function executeCommandInWorker(
        string|CommandDescriptorInterface $service,
        ?string                            $command      = null,
        array                              $parameters   = [],
        ?ExecutionContextInterface         $context      = null
    ): mixed;

    /**
     * @param array<string, mixed>               $parameters
     *
     */
    public function executeCommandInWorkerAsync(
        string|CommandDescriptorInterface $service,
        ?string                            $command      = null,
        array                              $parameters   = [],
        ?ExecutionContextInterface         $context      = null
    ): int|string;
}
