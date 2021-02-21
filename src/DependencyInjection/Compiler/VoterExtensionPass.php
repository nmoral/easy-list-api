<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Security\Voter\MainVoter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class VoterExtensionPass implements CompilerPassInterface
{
    private const TAG = 'app.voter';

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(MainVoter::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addExtension', [new Reference($id)]);
        }
    }
}
