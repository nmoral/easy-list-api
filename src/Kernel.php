<?php

namespace App;

use App\DependencyInjection\Compiler\ValidateSubscriberExtensionPass;
use App\DependencyInjection\Compiler\VoterExtensionPass;
use App\Event\Event;
use App\Event\PostValidateEvent;
use App\Event\PreValidateEvent;
use App\Event\ValidateEvent;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\DependencyInjection\AddEventAliasesPass;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/services.yaml')) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = \dirname(__DIR__).'/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } elseif (is_file($path = \dirname(__DIR__).'/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new VoterExtensionPass());
        $container->addCompilerPass(new ValidateSubscriberExtensionPass());
        $container->addCompilerPass(new AddEventAliasesPass([
            ValidateEvent::class => Event::VALIDATE_EVENT,
            PreValidateEvent::class => Event::PRE_VALIDATE_EVENT,
            PostValidateEvent::class => Event::POST_VALIDATE_EVENT,
        ]));
    }
}
