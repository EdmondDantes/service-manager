<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\DI\AutoResolverInterface;
use IfCastle\DI\BuilderInterface;
use IfCastle\DI\ContainerInterface;
use IfCastle\DI\DisposableInterface;

class BootloaderPublicInternalStrategy implements AutoResolverInterface, DisposableInterface
{
    private ContainerInterface|null $container = null;
    
    public const string PUBLIC_LOCATOR   = 'publicLocator';
    public const string INTERNAL_LOCATOR = 'internalLocator';
    public const string INTERNAL_EXECUTOR = 'internalExecutor';
    public const string PUBLIC_EXECUTOR = 'publicExecutor';
    
    public const string PUBLIC_REPOSITORY = 'publicRepository';
    
    public const string INTERNAL_REPOSITORY = 'internalRepository';
    
    public function __construct() {}
    
    #[\Override]
    public function resolveDependencies(ContainerInterface $container): void
    {
        $this->container = $container;
    }
    
    #[\Override]
    public function dispose(): void
    {
        $this->container = null;
    }
    
    public function __invoke(): void
    {
        if($this->container === null) {
            return;
        }
        
        $containerBuilder           = $this->container->resolveDependency(BuilderInterface::class);
        
        if(false === $containerBuilder->isBound(self::INTERNAL_EXECUTOR)) {
            $containerBuilder->bindConstructible(
                self::INTERNAL_EXECUTOR, InternalExecutor::class
            );
        }
        
        if(false === $containerBuilder->isBound(self::PUBLIC_EXECUTOR)) {
            $containerBuilder->bindConstructible(
                [self::PUBLIC_EXECUTOR, ExecutorInterface::class], InternalExecutor::class
            );
        }
        
        if(false === $containerBuilder->isBound(self::PUBLIC_LOCATOR)) {
            $containerBuilder->bindConstructible(
                self::PUBLIC_LOCATOR, InternalExecutor::class
            );
        }
        
        if(false === $containerBuilder->isBound(self::INTERNAL_LOCATOR)) {
            $containerBuilder->bindConstructible(
                self::INTERNAL_LOCATOR, InternalExecutor::class
            );
        }
        
        if(false === $containerBuilder->isBound(self::PUBLIC_REPOSITORY)) {
            $containerBuilder->bindConstructible(
                self::PUBLIC_REPOSITORY,
            );
        }
    }
}