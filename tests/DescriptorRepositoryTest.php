<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager;

use IfCastle\ServiceManager\ServiceMocks\ServiceLibrary;
use IfCastle\TypeDefinitions\Resolver\ExplicitTypeResolver;
use PHPUnit\Framework\TestCase;

class DescriptorRepositoryTest      extends TestCase
{
    private RepositoryReaderMemory $repositoryReader;
    private DescriptorRepository $descriptorRepository;
    
    #[\Override]
    protected function setUp(): void
    {
        $this->repositoryReader     = RepositoryReaderMemory::buildForTest();
        $this->descriptorRepository = new DescriptorRepository($this->repositoryReader, new ExplicitTypeResolver);
    }
    
    
    public function testFindServiceDescriptor(): void
    {
        $result                     = $this->descriptorRepository->findServiceDescriptor('ServiceLibrary');
        
        $this->assertNotNull($result);
        $this->assertInstanceOf(ServiceDescriptorInterface::class, $result);
        $this->assertEquals('ServiceLibrary', $result->getServiceName());
        $this->assertEquals(ServiceLibrary::class, $result->getClassName());
        
        // Check all methods by name
        $this->assertEquals(
            ['findBookByAuthor', 'addBook', 'getBooks', 'removeBook'],
            array_values(array_map(fn($method) => $method->getName(), $result->getServiceMethods()))
        );
    }
}
