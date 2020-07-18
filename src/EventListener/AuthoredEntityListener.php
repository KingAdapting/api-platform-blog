<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\AuthoredEntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Security;

class AuthoredEntityListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onEntityCreate(ViewEvent $viewEvent): void
    {
        $method = $viewEvent->getRequest()->getMethod();
        $entity = $viewEvent->getControllerResult();

        if ($entity instanceof AuthoredEntityInterface && $method === Request::METHOD_POST) {
            $entity->setAuthor($this->security->getUser());
        }
    }
}