<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface ExecutorInterface
{
    public function executeCommand(
        string|CommandDescriptorInterface $service,
        ?string                            $command      = null,
        array                             $parameters   = [],
        ?ExecutionContextInterface         $context      = null
    ): mixed;
}
