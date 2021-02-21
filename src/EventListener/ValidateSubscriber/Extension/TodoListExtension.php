<?php

namespace App\EventListener\ValidateSubscriber\Extension;

use App\Entity\Account\User;
use App\Entity\TodoList\ListManager;
use App\Entity\TodoList\TodoList;
use App\Event\PostValidateEvent;
use App\Event\PreValidateEvent;
use App\EventListener\AbstractExtensionListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TodoListExtension extends AbstractExtensionListener implements ValidateSubscriberInterface
{
    public function __construct(
            private TokenStorageInterface $tokenStorage
        ) {
        parent::__construct(TodoList::class);
    }

    public function preValidate(PreValidateEvent $event): void
    {
        /** @var TodoList $todoList */
        $todoList = $event->getEntity();
        if (null !== $todoList->getId()) {
            return;
        }
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $listManager = (new ListManager())
            ->setUser($user)
            ->setStatus(ListManager::STATUS_OWNER)
        ;

        $todoList->addListManager($listManager);
    }

    public function postValidate(PostValidateEvent $event): void
    {
    }
}
