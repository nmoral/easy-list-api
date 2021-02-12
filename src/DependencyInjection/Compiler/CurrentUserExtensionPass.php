<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Doctrine\CurrentUserExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CurrentUserExtensionPass implements CompilerPassInterface
{
    private const TAG = 'app.current_user_extension';

    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(CurrentUserExtension::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addExtension', [new Reference($id)]);
        }
    }
}
