<?php

declare(strict_types=1);

namespace App\EventListener\KernelViewSubscriber;

use App\Controller\AbstractApiController;
use App\Entity\AbstractEntity;
use App\Event\Event;
use App\Event\ValidateEvent;
use App\EventListener\AbstractKernelSubscriber;
use App\Serializer\CachedGroupResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class MainSubscriber extends AbstractKernelSubscriber implements EventSubscriberInterface
{
    public function __construct(
            CachedGroupResolver $groupResolver,
            private EntityManagerInterface $entityManager,
            private EventDispatcherInterface $eventDispatcher,
            private SerializerInterface $serializer
        ) {
        parent::__construct($groupResolver);
    }

    /**
     * @return string[][]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                'handleEntity',
            ],
        ];
    }

    public function handleEntity(ViewEvent $event): void
    {
        $object = $event->getControllerResult();
        $request = $event->getRequest();
        if (!$object instanceof AbstractEntity) {
            return;
        }

        $context = $request->attributes->get('context');

        if (in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT], true)) {
            $this->flush($event, $object);
            $context = $this->generateContext($object::class, AbstractApiController::ACTION_ITEM);
        }
        $httpCode = match ($request->getMethod()) {
            Request::METHOD_POST => 201,
            default => 200
        };

        $event->setResponse(new JsonResponse($this->serializer->serialize($object, 'json', $context), $httpCode, [], true));
    }

    private function flush(ViewEvent $event, AbstractEntity $object): void
    {
        $this->eventDispatcher->dispatch(new ValidateEvent($event->getRequest(), $object), Event::VALIDATE_EVENT);

        if (null === $object->getId()) {
            $this->entityManager->persist($object);
        }

        $this->entityManager->flush();
    }
}
