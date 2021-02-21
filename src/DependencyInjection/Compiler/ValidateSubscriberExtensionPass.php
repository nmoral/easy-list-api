<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\EventListener\ValidateSubscriber\MainSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ValidateSubscriberExtensionPass implements CompilerPassInterface
{
    private const TAG = 'app.validate_subscriber';

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(MainSubscriber::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addExtension', [new Reference($id)]);
        }
    }
}
