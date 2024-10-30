<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface WorkerExecutorInterface
{
    /**
     * @param string|CommandDescriptorInterface  $service
     * @param string|null                        $command
     * @param array<string, mixed>               $parameters
     * @param ExecutionContextInterface|null     $context
     *
     * @return mixed
     */
    public function executeCommandInWorker(
        string|CommandDescriptorInterface $service,
        ?string                            $command      = null,
        array                              $parameters   = [],
        ?ExecutionContextInterface         $context      = null
    ): mixed;

    /**
     * @param string|CommandDescriptorInterface  $service
     * @param string|null                        $command
     * @param array<string, mixed>               $parameters
     * @param ExecutionContextInterface|null     $context
     *
     * @return int
     */
    public function executeCommandInWorkerAsync(
        string|CommandDescriptorInterface $service,
        ?string                            $command      = null,
        array                             $parameters    = [],
        ?ExecutionContextInterface         $context      = null
    ): int;
}
