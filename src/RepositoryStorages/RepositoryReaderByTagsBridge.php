<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager\RepositoryStorages;

final readonly class RepositoryReaderByTagsBridge implements RepositoryReaderInterface
{
    /**
     * @param array<string> $tags
     */
    public function __construct(private RepositoryReaderByTagsInterface $repositoryReader, private array $tags) {}

    #[\Override]
    public function getServicesConfig(): array
    {
        return $this->repositoryReader->getServicesConfigByTags(...$this->tags);
    }

    #[\Override]
    public function findServiceConfig(string $serviceName): array|null
    {
        return $this->repositoryReader->findServiceConfigByTags($serviceName, ...$this->tags);
    }
}
