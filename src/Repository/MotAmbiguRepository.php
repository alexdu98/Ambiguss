<?php

namespace App\Repository;

use App\Entity\MotAmbigu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MotAmbiguRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MotAmbigu::class);
    }

    /**
     * Retourne les mots ambigus de la glose
     *
     * @param string $valeurG
     * @return array
     */
    public function findMotsAmbigusByValueGloseValue(string $valeurG){
        return $this->createQueryBuilder('ma')->select('ma.id, ma.valeur')
            ->innerJoin("ma.gloses", "g", "WITH", "g.valeur = :valeurG")->setParameter('valeurG', $valeurG)
            ->orderBy("ma.valeur")
            ->getQuery()->getResult();
    }

    /**
     * Retourne les valeurs possibles de mots ambigus (autocomplete)
     *
     * @param string $valeur
     * @return array
     */
	public function findByValeurAutoComplete(string $valeur)
	{
		return $this->createQueryBuilder('ma')->select('ma.id, ma.valeur')
			->where('ma.valeur LIKE :valeur')->setParameter('valeur', $valeur . '%')
			->getQuery()->getResult();
	}

    /**
     * Retourne les mots ambigus signalés
     *
     * @return array
     */
    public function getSignale()
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.auteur', 'a', 'WITH', 'g.auteur = a.id')->addSelect('a')
            ->leftJoin('g.modificateur', 'm', 'WITH', 'g.modificateur = m.id')->addSelect('m')
            ->where('g.signale = 1')
            ->andWhere('g.visible=1')
            ->getQuery()->getResult();
    }

    /**
     * Retourne les informations pour l'export de mots ambigus
     *
     * @return array
     */
    public function export()
    {
        $query = $this->createQueryBuilder('ma')
            ->select('ma.id idma, ma.valeur motAmbigu, g.valeur glose')
            ->innerJoin('ma.gloses', 'g')
            ->where('ma.visible = 1')
            ->orderBy('ma.id, g.id', 'ASC')
            ->getQuery();

        return $query->getArrayResult();
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

		$array['total'] = $this->createQueryBuilder('ma')
			                  ->select('count(ma) total')
			                  ->getQuery()->getSingleResult()['total'];

		$dateJ30 = new \DateTime();
		$dateJ30->setTimestamp($dateJ30->getTimestamp() - (3600 * 24 * 30));
		$array['creationJ30'] = $this->createQueryBuilder('ma')
			                        ->select('count(ma) creationJ30')
			                        ->where('ma.dateCreation > :j30')->setParameter('j30', $dateJ30)
			                        ->getQuery()->getSingleResult()['creationJ30'];

		$dateJ7 = new \DateTime();
		$dateJ7->setTimestamp($dateJ7->getTimestamp() - (3600 * 24 * 7));
		$array['creationJ7'] = $this->createQueryBuilder('ma')
			                       ->select('count(ma) creationJ7')
			                       ->where('ma.dateCreation > :j7')->setParameter('j7', $dateJ7)
			                       ->getQuery()->getSingleResult()['creationJ7'];

		$dateH24 = new \DateTime();
		$dateH24->setTimestamp($dateH24->getTimestamp() - (3600 * 24));
		$array['creationH24'] = $this->createQueryBuilder('ma')
			                        ->select('count(ma) creationH24')
			                        ->where('ma.dateCreation > :h24')->setParameter('h24', $dateH24)
			                        ->getQuery()->getSingleResult()['creationH24'];

		$array['signale'] = $this->createQueryBuilder('ma')
			                    ->select('count(ma) signale')
			                    ->where('ma.signale = 1')
			                    ->getQuery()->getSingleResult()['signale'];

		return $array;
	}

}
