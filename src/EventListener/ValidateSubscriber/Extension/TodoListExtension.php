<?php


namespace App\EventListener\ValidateSubscriber\Extension;

use App\Entity\Account\User;
use App\Entity\TodoList\ListManager;
use App\Entity\TodoList\TodoList;
use App\EventListener\AbstractExtensionListener;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TodoListExtension extends AbstractExtensionListener implements ValidateSubscriberInterface
{
    #[Pure] public function __construct(
        private TokenStorageInterface $tokenStorage
    )
    {
        parent::__construct(TodoList::class);
    }

    #[NoReturn] public function preValidate(ViewEvent $event)
    {
        /** @var TodoList $todoList */
        $todoList = $event->getControllerResult();
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

    public function postValidate(ViewEvent $event)
    {
    }
}