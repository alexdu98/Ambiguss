<?php

namespace App\Repository;

use App\Entity\Badge;
use App\Entity\Membre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BadgeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }

    public function getBestWinForMembre(Membre $membre) {
        // Récupère l'ordre le plus haut pour chaque type de badge gagné
        $bestWin = $this->createQueryBuilder('b')
            ->select('b.type, max(b.ordre) ordre')
            ->innerJoin('b.membres', 'm', 'WITH', 'm.membre = :membre')
            ->groupBy('b.type')
            ->setParameter('membre', $membre)
            ->getQuery()->getResult();

        // Récupère tous les badges gagnés
        $badgesUser = $this->createQueryBuilder('b')
            ->addSelect('m.dateObtention')
            ->innerJoin('b.membres', 'm', 'WITH', 'm.membre = :membre')
            ->orderBy('m.dateObtention', 'desc')
            ->setParameter('membre', $membre)
            ->getQuery()->getResult();

        // Garde le badge avec l'ordre le plus haut pour chaque type
        foreach ($badgesUser as $key => $badge) {
            $keyFind = array_search($badge[0]->getType(), array_column($bestWin, 'type'));
            if ($badge[0]->getOrdre() != $bestWin[$keyFind]['ordre']) {
                unset($badgesUser[$key]);
            }
        }

        return $badgesUser;
    }

    public function getNextWinForMembre(Membre $membre) {
        // Récupère l'ordre le plus haut pour chaque type de badge gagné
        $bestWin = $this->createQueryBuilder('b')
            ->select('b.type, max(b.ordre) ordre')
            ->innerJoin('b.membres', 'm', 'WITH', 'm.membre = :membre')
            ->groupBy('b.type')
            ->setParameter('membre', $membre)
            ->getQuery()->getResult();

        // Récupère tous les badges
        $badges = $this->createQueryBuilder('b')
            ->getQuery()->getResult();

        // Garde le badge suivant à gagner pour chaque type
        foreach ($badges as $key => $badge) {
            $keyFind = array_search($badge->getType(), array_column($bestWin, 'type'));
            if ($keyFind !== false && $bestWin[$keyFind]['ordre'] + 1 != $badge->getOrdre() || $keyFind === false && $badge->getOrdre() != 1) {
                unset($badges[$key]);
            }
        }

        return $badges;
    }

    public function getNotWinYetForMembreAndType(Membre $membre, $type)
    {
        $query = $this->createQueryBuilder('b')
            ->leftJoin('b.membres', 'm', 'WITH', 'm.membre = :membre')
            ->where('b.type = :type')
            ->andWhere('m is null')
            ->orderBy('b.ordre', 'asc')
            ->setParameter('membre', $membre)
            ->setParameter('type', $type);

        return $query->getQuery()->getResult();
    }

}
