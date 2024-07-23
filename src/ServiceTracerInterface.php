<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface ServiceTracerInterface
{
    public function startServiceCall(string $serviceName, string $method, array $parameters): void;
    public function recordResult(mixed $value): void;
}