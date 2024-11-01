<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

use Attribute;
use IfCastle\DI\ContainerInterface;
use IfCastle\DI\Dependency;
use IfCastle\DI\DependencyInterface;
use IfCastle\DI\DescriptorInterface;
use IfCastle\DI\FactoryInterface;
use IfCastle\Exceptions\LogicalException;
use IfCastle\TypeDefinitions\TypesEnum;

/**
 * Attribute for dependency description: service configuration.
 * This is a special attribute that specifies that the dependency should be resolved into a constant expression
 * with the service configuration from the global `service registry`.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class ServiceConfig extends Dependency implements FactoryInterface
{
    public function __construct(
        string            $key = '',
        bool              $isRequired = true,
        string            $property = ''
    ) {
        parent::__construct($key, TypesEnum::ARRAY->value, $isRequired, false, $property, true, []);
    }

    #[\Override]
    public function getFactory(): FactoryInterface|null
    {
        return $this;
    }

    /**
     * @throws LogicalException
     */
    #[\Override]
    public function create(ContainerInterface   $container,
        DescriptorInterface  $descriptor,
        ?DependencyInterface $forDependency = null
    ): mixed {
        if ($forDependency === null) {
            return null;
        }

        if ($forDependency instanceof ServiceDescriptorInterface) {
            return $forDependency->getServiceConfig();
        }

        throw new LogicalException([
            'template'              => 'ServiceConfig can only be used with ServiceDescriptorInterface, but used with {forDependency}',
            'forDependency'         => $forDependency::class,
        ]);
    }
}
