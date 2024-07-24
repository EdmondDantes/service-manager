<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

interface DescriptorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array;
    
    public function findAttribute(string $name): mixed;
}