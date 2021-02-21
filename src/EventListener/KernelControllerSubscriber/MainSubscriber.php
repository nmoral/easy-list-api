<?php

declare(strict_types=1);

namespace App\EventListener\KernelControllerSubscriber;

use App\Controller\AbstractApiController;
use App\EventListener\AbstractKernelSubscriber;
use App\Serializer\CachedGroupResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class MainSubscriber extends AbstractKernelSubscriber implements EventSubscriberInterface
{
    public function __construct(
        CachedGroupResolver $groupResolver,
        private SerializerInterface $serializer,
        private CacheInterface $cache,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct($groupResolver);
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'handle',
        ];
    }

    public function handle(ControllerEvent $event): void
    {
        $controller = $event->getController();
        $method = null;
        if (is_array($controller)) {
            $method = $controller[1];
            $controller = $controller[0];
        }

        if (!$controller instanceof AbstractApiController) {
            return;
        }
        $context = $this->generateContext($controller->getClassName(), $method);
        $request = $event->getRequest();
        if (Request::METHOD_PUT === $request->getMethod()) {
            $object = $this->entityManager->find($controller->getClassName(), $request->get('id'));
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $object;
        }
        $res = $this->serializer->deserialize($event->getRequest()->getContent(), $controller->getClassName(), 'json', $context);

        $request->attributes->set('data', $res);
        $request->attributes->set('context', $context);
    }
}
