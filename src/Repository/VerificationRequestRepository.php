<?php

namespace App\Repository;

use App\Entity\VerificationRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VerificationRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerificationRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerificationRequest[]    findAll()
 * @method VerificationRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerificationRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerificationRequest::class);
    }
}
