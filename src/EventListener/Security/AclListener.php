<?php

declare(strict_types=1);

namespace App\EventListener\Security;

use App\Entity\AbstractEntity;
use App\Security\Voter\MainVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class AclListener
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private RequestStack $requestStack,
        private TokenStorageInterface $tokenStorage
    ) {
    }

    public function onKernelView(ViewEvent $event): void
    {
        $object = $event->getControllerResult();

        if (!$object instanceof AbstractEntity && !$object instanceof \Traversable) {
            return;
        }

        $action = $this->getActionForCurrentRequest();
        if (!$action) {
            return;
        }

        if (!$this->authorizationChecker->isGranted($action, $object)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Get action term for current request.
     */
    private function getActionForCurrentRequest(): ?string
    {
        $action = null;
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            switch ($request->getMethod()) {
                case Request::METHOD_POST:
                    $action = MainVoter::ACTION_CREATE;

                    break;
                case Request::METHOD_GET:
                    $action = MainVoter::ACTION_RETRIEVE;

                    break;
                case Request::METHOD_PUT:
                    $action = MainVoter::ACTION_UPDATE;

                    break;
                case Request::METHOD_DELETE:
                    $action = MainVoter::ACTION_DELETE;

                    break;
            }
        }

        return $action;
    }
}
