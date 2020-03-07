<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class SignalementRepository extends EntityRepository
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

        $array['total'] = $this->createQueryBuilder('s')
            ->select('count(s) total')
            ->getQuery()->getSingleResult()['total'];

        $dateJ30 = new \DateTime();
        $dateJ30->setTimestamp($dateJ30->getTimestamp() - (3600 * 24 * 30));
        $array['creationJ30'] = $this->createQueryBuilder('s')
            ->select('count(s) creationJ30')
            ->where('s.dateCreation > :j30')->setParameter('j30', $dateJ30)
            ->getQuery()->getSingleResult()['creationJ30'];

        $dateJ7 = new \DateTime();
        $dateJ7->setTimestamp($dateJ7->getTimestamp() - (3600 * 24 * 7));
        $array['creationJ7'] = $this->createQueryBuilder('s')
            ->select('count(s) creationJ7')
            ->where('s.dateCreation > :j7')->setParameter('j7', $dateJ7)
            ->getQuery()->getSingleResult()['creationJ7'];

        $dateH24 = new \DateTime();
        $dateH24->setTimestamp($dateH24->getTimestamp() - (3600 * 24));
        $array['creationH24'] = $this->createQueryBuilder('s')
            ->select('count(s) creationH24')
            ->where('s.dateCreation > :h24')->setParameter('h24', $dateH24)
            ->getQuery()->getSingleResult()['creationH24'];

        $array['enAttente'] = $this->createQueryBuilder('s')
            ->select('count(s) enAttente')
            ->where('s.verdict is null')
            ->getQuery()->getSingleResult()['enAttente'];

        return $array;
    }

    /**
     * Retourne le nombre de signalements en cours
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countEnCours()
    {
        $nbForPhrase = $this->createQueryBuilder('s')
            ->select('count(s) enCours')
            ->leftJoin('AppBundle:Phrase', 'p', 'WITH', 'p.id = s.objetId')
            ->innerJoin('s.typeObjet', 'to')
            ->where('to.nom = \'Phrase\' AND s.verdict IS NULL')
            ->getQuery()->getSingleResult()['enCours'];

        $nbForGlose = $this->createQueryBuilder('s')
            ->select('count(s) enCours')
            ->leftJoin('AppBundle:Glose', 'g', 'WITH', 'g.id = s.objetId')
            ->innerJoin('s.typeObjet', 'to')
            ->where('to.nom = \'Glose\' AND s.verdict IS NULL')
            ->getQuery()->getSingleResult()['enCours'];

        $nbForMembre = $this->createQueryBuilder('s')
            ->select('count(s) enCours')
            ->leftJoin('AppBundle:Membre', 'm', 'WITH', 'm.id = s.objetId')
            ->innerJoin('s.typeObjet', 'to')
            ->where('to.nom = \'Membre\' AND s.verdict IS NULL')
            ->getQuery()->getSingleResult()['enCours'];

        return $nbForPhrase + $nbForGlose + $nbForMembre;
    }

    public function getAllWithObject() {
        $query = $this->createQueryBuilder('s')
            ->addSelect('s, to, cj, a, juge, v, p.contenu, g.valeur, m.username')
            ->innerJoin('s.typeObjet', 'to')
            ->innerJoin('s.categorieSignalement', 'cj')
            ->innerJoin('s.auteur', 'a')
            ->leftJoin('s.juge', 'juge')
            ->leftJoin('s.verdict', 'v')
            ->leftJoin('AppBundle:Phrase', 'p', 'WITH', 'p.id = s.objetId')
            ->leftJoin('AppBundle:Glose', 'g', 'WITH', 'g.id = s.objetId')
            ->leftJoin('AppBundle:Membre', 'm', 'WITH', 'm.id = s.objetId');

        return $query->getQuery()->getResult();
    }

}
