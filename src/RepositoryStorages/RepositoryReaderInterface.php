<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

interface RepositoryReaderInterface
{
    /**
     * @return array<string, array>
     */
    public function getServicesConfig(): array;
}