<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractApiController;
use App\Entity\AbstractEntity;
use App\Entity\Account\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
#[Route(path: '/user', name: 'user_')]
class UserController extends AbstractApiController
{
    /**
     * UserController constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(
        private UserRepository $repository
    ) {
        parent::__construct(User::class);
    }

    /**
     * @return User
     */
    #[Route(path: '', name: 'create', methods: [Request::METHOD_POST])]
    public function create(Request $request): User
    {
        return $request->get('data');
    }

    #[Route(path: '/{id}', name: 'edit', methods: [Request::METHOD_PUT])]
    public function edit(Request $request): User
    {
        return $request->get('data');
    }

    public function getItem(Request $request): AbstractEntity
    {
        /** @var User|null $object */
        $object = $this->repository->find($request->get('id'));
        if (null === $object) {
            throw new NotFoundHttpException();
        }

        return $object;
    }

    /**
     * @return mixed[]
     */
    public function getList(Request $request): array
    {
        return [];
    }

    public function delete(Request $request): void
    {
    }
}
