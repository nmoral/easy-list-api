<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AbstractEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractApiController extends AbstractController
{
    public const ACTION_ITEM = 'getItem';

    public const ACTION = [
        self::ACTION_ITEM,
        'create',
        'edit',
        'getList',
        'delete',
    ];

    /**
     * AbstractApiController constructor.
     */
    public function __construct(
        private string $className
    ) {
    }

    abstract public function create(Request $request): AbstractEntity;

    abstract public function edit(Request $request): AbstractEntity;

    abstract public function getItem(Request $request): AbstractEntity;

    /**
     * @return mixed[]
     */
    abstract public function getList(Request $request): array;

    abstract public function delete(Request $request): void;

    /**
     * @return class-string
     */
    public function getClassName(): string
    {
        /* @phpstan-ignore-next-line */
        return $this->className;
    }
}
