<?php


namespace App\Action\Collection\User;

use App\Entity\Account\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CreateAction
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private RequestStack $requestStack
    )
    {}

    public function __invoke(): ?User
    {
        $request = $this->getRequest();
        if (null === $request) {
            return null;
        }

        return User::createFromJson($request->getContent());
    }

    private function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}