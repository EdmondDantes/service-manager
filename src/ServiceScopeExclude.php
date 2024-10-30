<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

use Attribute;
use IfCastle\TypeDefinitions\NativeSerialization\AttributeNameInterface;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class ServiceScopeExclude implements AttributeNameInterface
{
    public array $scopes;

    public function __construct(string ...$scopes)
    {
        $this->scopes = $scopes;
    }

    #[\Override]
    public function getAttributeName(): string
    {
        return 'serviceScopeExclude';
    }
}
