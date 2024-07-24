<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface MethodDescriptorInterface
{
    public function getServiceClass(): string;
    
    public function getMethod(): string;
    
    public function getParameters(): array;
}