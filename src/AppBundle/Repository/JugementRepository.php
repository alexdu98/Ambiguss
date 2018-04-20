<?php

namespace AppBundle\Repository;

class JugementRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Retourne un tableau de statistiques de l'entité
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getStat()
    {
        $array = array();

        $array['total'] = $this->createQueryBuilder('j')
            ->select('count(j) total')
            ->getQuery()->getSingleResult()['total'];

        $dateJ30 = new \DateTime();
        $dateJ30->setTimestamp($dateJ30->getTimestamp() - (3600 * 24 * 30));
        $array['creationJ30'] = $this->createQueryBuilder('j')
            ->select('count(j) creationJ30')
            ->where('j.dateCreation > :j30')->setParameter('j30', $dateJ30)
            ->getQuery()->getSingleResult()['creationJ30'];

        $dateJ7 = new \DateTime();
        $dateJ7->setTimestamp($dateJ7->getTimestamp() - (3600 * 24 * 7));
        $array['creationJ7'] = $this->createQueryBuilder('j')
            ->select('count(j) creationJ7')
            ->where('j.dateCreation > :j7')->setParameter('j7', $dateJ7)
            ->getQuery()->getSingleResult()['creationJ7'];

        $dateH24 = new \DateTime();
        $dateH24->setTimestamp($dateH24->getTimestamp() - (3600 * 24));
        $array['creationH24'] = $this->createQueryBuilder('j')
            ->select('count(j) creationH24')
            ->where('j.dateCreation > :h24')->setParameter('h24', $dateH24)
            ->getQuery()->getSingleResult()['creationH24'];

        $array['enAttente'] = $this->createQueryBuilder('j')
            ->select('count(j) enAttente')
            ->where('j.verdict is null')
            ->getQuery()->getSingleResult()['enAttente'];

        return $array;
    }

}
