<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\TypeDefinitions\Resolver\DefaultResolver;
use PHPUnit\Framework\TestCase;

class DescriptorRepositoryTest      extends TestCase
{
    private RepositoryReaderMemory $repositoryReader;
    private DescriptorRepository $descriptorRepository;
    
    #[\Override]
    protected function setUp(): void
    {
        $this->repositoryReader     = RepositoryReaderMemory::buildForTest();
        $this->descriptorRepository = new DescriptorRepository($this->repositoryReader, new DefaultResolver());
    }
    
    
    public function testFindServiceDescriptor(): void
    {
        $result                     = $this->descriptorRepository->findServiceDescriptor('ServiceLibrary');
        
        $this->assertNotNull($result);
        $this->assertInstanceOf(ServiceDescriptorInterface::class, $result);
    }
}
