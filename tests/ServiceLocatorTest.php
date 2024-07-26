<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use PHPUnit\Framework\TestCase;

class ServiceLocatorTest            extends TestCase
{
    private RepositoryReaderMemory $repositoryReader;
    
    
    #[\Override]
    protected function setUp(): void
    {
        $this->repositoryReader     = RepositoryReaderMemory::buildForTest();
        
    }
    
    public function testFindServiceClass(): void
    {
    
    }
}
