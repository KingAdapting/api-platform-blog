<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\VerificationRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class VerificationRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerificationRequest::class);
    }

    public function hasByUser(UserInterface $user): bool
    {
        $queryBuilder = $this->createQueryBuilder('v');

        $queryBuilder
            ->select($queryBuilder->expr()->count('v.id'))
            ->where($queryBuilder->expr()->eq('v.author', ':user'))
            ->setParameter('user', $user)
        ;

        return $queryBuilder->getQuery()->getSingleScalarResult() > 0;
    }
}
