<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\DependencyInterface;

interface ServiceDescriptorInterface extends DependencyInterface
{
    public function getServiceName(): string;
    
    public function getClassName(): string;
    
    public function isServiceActive(): bool;
    
    public function getServiceConfig(): array;
}