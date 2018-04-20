<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Membre;

class PartieRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Retourne le nombre de partie jouée du membre
     *
     * @param Membre $membre
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function countAllGamesByMembre(Membre $membre)
	{
		return $this->createQueryBuilder('p')
			->select('count(p) nbParties')
			->where('p.joueur = :membre')->setParameter('membre', $membre)
			->andWhere('p.joue = 1')
			->getQuery()->getSingleResult();
	}

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

		$array['total'] = $this->createQueryBuilder('pa')
			                  ->select('count(pa) total')
			                  ->getQuery()->getSingleResult()['total'];

		$dateJ30 = new \DateTime();
		$dateJ30->setTimestamp($dateJ30->getTimestamp() - (3600 * 24 * 30));
		$array['joueJ30'] = $this->createQueryBuilder('pa')
			                    ->select('count(pa) joueJ30')
			                    ->where('pa.datePartie > :j30')->setParameter('j30', $dateJ30)
			                    ->getQuery()->getSingleResult()['joueJ30'];

		$dateJ7 = new \DateTime();
		$dateJ7->setTimestamp($dateJ7->getTimestamp() - (3600 * 24 * 7));
		$array['joueJ7'] = $this->createQueryBuilder('pa')
			                   ->select('count(pa) joueJ7')
			                   ->where('pa.datePartie > :j7')->setParameter('j7', $dateJ7)
			                   ->getQuery()->getSingleResult()['joueJ7'];

		$dateH24 = new \DateTime();
		$dateH24->setTimestamp($dateH24->getTimestamp() - (3600 * 24));
		$array['joueH24'] = $this->createQueryBuilder('pa')
			                    ->select('count(pa) joueH24')
			                    ->where('pa.datePartie > :h24')->setParameter('h24', $dateH24)
			                    ->getQuery()->getSingleResult()['joueH24'];

		$array['moyGain'] = $this->createQueryBuilder('pa')
			                    ->select('avg(pa.gainJoueur) moyGain')
			                    ->getQuery()->getSingleResult()['moyGain'];

		return $array;
	}

}
