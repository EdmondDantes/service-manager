<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface ServiceTracerInterface
{
    public function startServiceCall(string $serviceName, ServiceDescriptorInterface $serviceDescriptor, string $method, array $parameters): void;

    public function recordResult(mixed $value): void;

    public function recordException(\Throwable $exception): void;

    public function end(): void;
}
