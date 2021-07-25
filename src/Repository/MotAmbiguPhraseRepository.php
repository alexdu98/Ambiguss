<?php

namespace App\Repository;

use App\Entity\MotAmbiguPhrase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MotAmbiguPhraseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MotAmbiguPhrase::class);
    }
}
