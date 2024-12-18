<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\ServiceManager\RepositoryStorages\ServiceCollectionInterface;

trait ServiceConfigReaderTrait
{
    abstract protected function load(): void;

    /**
     * @var array<array<array<mixed>>>
     */
    protected array $data           = [];

    #[\Override]
    public function getServiceCollection(
        string|null $serviceName = null,
        string|null $packageName = null,
        string|null $suffix = null,
        bool|null   $isActive = null,
        array       $tags = []
    ): array {
        $this->load();

        if ($serviceName === null && $packageName === null && $suffix === null && $isActive === null && $tags === []) {
            return $this->data;
        }

        $collection                 = [];

        if ($serviceName !== null && \array_key_exists($serviceName, $this->data)) {
            $set                    = [$serviceName => $this->data[$serviceName]];
        } else {
            $set                    = & $this->data;
        }

        foreach ($set as $service => $implementations) {
            foreach ($implementations as $serviceSuffix => $serviceConfig) {

                if (($suffix !== null && $suffix !== (string) $serviceSuffix)
                    || ($isActive !== null && $serviceConfig[ServiceCollectionInterface::IS_ACTIVE] !== $isActive)
                    || ($packageName !== null && $serviceConfig[ServiceCollectionInterface::PACKAGE] !== $packageName)
                    || ($tags !== [] && \array_intersect($serviceConfig[ServiceCollectionInterface::TAGS] ?? [], $tags) === [])
                    || \array_intersect($serviceConfig[ServiceCollectionInterface::EXCLUDE_TAGS] ?? [], $tags) !== []) {
                    continue;
                }

                $collection[$service][(string) $serviceSuffix] = $serviceConfig;
            }
        }

        return $collection;
    }

    #[\Override]
    public function getServicesConfig(): array
    {
        $this->load();

        $collection                 = [];

        foreach ($this->data as $service => $implementations) {
            foreach ($implementations as $serviceSuffix => $serviceConfig) {
                if (($serviceConfig[ServiceCollectionInterface::IS_ACTIVE] ?? false) === true) {
                    $collection[$service][(string) $serviceSuffix] = $serviceConfig;
                    break;
                }
            }
        }

        return $collection;
    }

    #[\Override]
    public function findServiceConfig(string $serviceName): array|null
    {
        $this->load();

        foreach ($this->data[$serviceName] ?? [] as $serviceConfig) {
            if (($serviceConfig[ServiceCollectionInterface::IS_ACTIVE] ?? false) === true) {
                return $serviceConfig;
            }
        }

        return null;
    }
}
