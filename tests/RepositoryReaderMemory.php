<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\ServiceManager\RepositoryStorages\RepositoryReaderInterface;
use IfCastle\ServiceManager\ServiceMocks\ServiceLibrary;
use IfCastle\ServiceManager\ServiceMocks\ServiceMailer;

readonly class RepositoryReaderMemory implements RepositoryReaderInterface
{
    public static function buildForTest(): self
    {
        return new self([
            'ServiceLibrary'        => [
                'class'             => ServiceLibrary::class,
                'isActive'          => true,
                'config'            => [],
            ],
            'ServiceMailer'         => [
                'class'             => ServiceMailer::class,
                'isActive'          => true,
                'config'            => []
            ],
            'ServiceMailerInactive' => [
                'class'             => 'fakeClass',
                'isActive'          => false,
                'config'            => []
            ]
        ]);
    }
    
    public function __construct(public array $servicesConfig = []) {}
    
    #[\Override]
    public function getServicesConfig(): array
    {
        return $this->servicesConfig;
    }
}