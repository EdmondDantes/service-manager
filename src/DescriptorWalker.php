<?php

declare(strict_types=1);

namespace IfCastle\ServiceManager;

final class DescriptorWalker
{
    public static function walk(DescriptorRepositoryInterface $descriptorRepository): iterable
    {
        foreach ($descriptorRepository->getServiceDescriptorList() as $serviceDescriptor) {
            foreach ($serviceDescriptor->getServiceMethods() as $serviceMethod) {
                $isBreak = yield $serviceDescriptor->getServiceName() => $serviceMethod;

                if (true === $isBreak) {
                    return;
                }
            }
        }
    }

    public static function walkWithService(DescriptorRepositoryInterface $descriptorRepository): iterable
    {
        foreach ($descriptorRepository->getServiceDescriptorList() as $serviceDescriptor) {
            foreach ($serviceDescriptor->getServiceMethods() as $serviceMethod) {
                $isBreak = yield $serviceDescriptor->getServiceName() => [$serviceDescriptor, $serviceMethod];

                if (true === $isBreak) {
                    return;
                }
            }
        }
    }
}
