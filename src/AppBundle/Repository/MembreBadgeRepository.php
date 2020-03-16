<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class MembreBadgeRepository extends EntityRepository
{
    /**
     * Retourne un tableau de statistiques de l'entité
     *
     * @return array
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getStat()
    {
        $array = array();

        $array['total'] = $this->createQueryBuilder('mb')
            ->select('count(mb) total')
            ->getQuery()->getSingleResult()['total'];

        $dateJ30 = new \DateTime();
        $dateJ30->setTimestamp($dateJ30->getTimestamp() - (3600 * 24 * 30));
        $array['obtentionJ30'] = $this->createQueryBuilder('mb')
            ->select('count(mb) obtentionJ30')
            ->where('mb.dateObtention > :j30')->setParameter('j30', $dateJ30)
            ->getQuery()->getSingleResult()['obtentionJ30'];

        $dateJ7 = new \DateTime();
        $dateJ7->setTimestamp($dateJ7->getTimestamp() - (3600 * 24 * 7));
        $array['obtentionJ7'] = $this->createQueryBuilder('mb')
            ->select('count(mb) obtentionJ7')
            ->where('mb.dateObtention > :j7')->setParameter('j7', $dateJ7)
            ->getQuery()->getSingleResult()['obtentionJ7'];

        $dateH24 = new \DateTime();
        $dateH24->setTimestamp($dateH24->getTimestamp() - (3600 * 24));
        $array['obtentionH24'] = $this->createQueryBuilder('mb')
            ->select('count(mb) obtentionH24')
            ->where('mb.dateObtention > :h24')->setParameter('h24', $dateH24)
            ->getQuery()->getSingleResult()['obtentionH24'];

        return $array;
    }
}
