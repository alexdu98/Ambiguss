<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Visite;

class VisiteRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param mixed $ip Adresse IP de l'utilisateur
     * @param mixed $userAgent UserAgent de l'utilisateur
     * @param int $seconds Nombre de secondes d'une période
     * @return mixed La visite la plus récente de la période s'il y en a une, null sinon
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastVisitPeriod(Visite $visite, int $seconds)
    {
        $dateLastPeriod = (new \DateTime())->modify('-' . $seconds . ' seconds');

        $query = $this->createQueryBuilder('v')
            ->where("v.ip = :ip")->setParameter("ip", $visite->getIp())
            ->andWhere("v.userAgent = :userAgent")->setParameter("userAgent", $visite->getUserAgent())
            ->andWhere("v.dateVisite > :dateVisite")->setParameter("dateVisite", $dateLastPeriod)
            ->orderBy("v.dateVisite", 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Retourne un tableau de statistiques de l'entité
     *
     * @return array Un tableau contenant des statistiques
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function getStat()
	{
		$array = array();

		$array['total'] = $this->createQueryBuilder('v')
			                  ->select('count(v) total')
			                  ->getQuery()->getSingleResult()['total'];

		$dateJ30 = new \DateTime();
		$dateJ30->setTimestamp($dateJ30->getTimestamp() - (3600 * 24 * 30));
		$array['j30'] = $this->createQueryBuilder('v')
			                ->select('count(v) j30')
			                ->where('v.dateVisite > :j30')->setParameter('j30', $dateJ30)
			                ->getQuery()->getSingleResult()['j30'];

		$dateJ7 = new \DateTime();
		$dateJ7->setTimestamp($dateJ7->getTimestamp() - (3600 * 24 * 7));
		$array['j7'] = $this->createQueryBuilder('v')
			               ->select('count(v) j7')
			               ->where('v.dateVisite > :j7')->setParameter('j7', $dateJ7)
			               ->getQuery()->getSingleResult()['j7'];

		$dateH24 = new \DateTime();
		$dateH24->setTimestamp($dateH24->getTimestamp() - (3600 * 24));
		$array['h24'] = $this->createQueryBuilder('v')
			                ->select('count(v) h24')
			                ->where('v.dateVisite > :h24')->setParameter('h24', $dateH24)
			                ->getQuery()->getSingleResult()['h24'];

		return $array;
	}

}
