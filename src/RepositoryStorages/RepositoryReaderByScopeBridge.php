<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

final readonly class RepositoryReaderByScopeBridge implements RepositoryReaderInterface
{
    public function __construct(private RepositoryReaderByScopeInterface $repositoryReader, private array $scopes) {}
    
    #[\Override]
    public function getServicesConfig(): array
    {
        return $this->repositoryReader->getServicesConfigByScope(...$this->scopes);
    }
}