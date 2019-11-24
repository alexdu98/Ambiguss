<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Membre;
use Doctrine\ORM\EntityRepository;

class HistoriqueRepository extends EntityRepository
{
    /**
     * Retourne le nombre d'historique
     *
     * @param Membre $membre
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAllByMembre(Membre $membre)
    {
        return $this->createQueryBuilder('h')
            ->select('count(h) nbHistorique')
            ->where('h.membre = :membre')->setParameter('membre', $membre)
            ->getQuery()->getOneOrNullResult()['nbHistorique'];
    }

    /**
     * Retourne les lignes correspondantes aux différentes conditions (dataTable AJAX)
     *
     * @param $start Numéro de la première ligne retournée
     * @param $length Nombre de ligne à retourner
     * @param $orders Ordre du tri
     * @param $search Recherche de caractère
     * @param $columns Colonnes
     * @param null $otherConditions Autres conditions
     * @return array Tableau des données trouvées
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null)
    {
        $query = $this->createQueryBuilder('h')
            ->where('h.membre = :membre')
            ->setParameters($otherConditions)
            ->orderBy('h.dateAction', $orders[0]['dir'])
            ->setFirstResult($start)
            ->setMaxResults($length);

        $countQuery = $this->createQueryBuilder('h')
            ->select('COUNT(h)')
            ->where('h.membre = :membre')
            ->setParameters($otherConditions);

        if(!empty($search['value'])){
            $query
                ->andWhere('DATE_FORMAT(h.dateAction, \'%d/%m/%Y %H:%i\') like :search OR h.valeur like :search')
                ->setParameter('search', '%' . $search['value'] . '%');

            $countQuery
                ->andWhere('DATE_FORMAT(h.dateAction, \'%d/%m/%Y %H:%i\') like :search OR h.valeur like :search')
                ->setParameter('search', '%' . $search['value'] . '%');
        }

        $res = array(
            'results' => $query->getQuery()->getArrayResult(),
            'countResult' => $countQuery->getQuery()->getSingleScalarResult()
        );

        return $res;
    }

}
