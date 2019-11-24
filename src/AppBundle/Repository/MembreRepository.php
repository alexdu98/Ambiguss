<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Membre;
use Doctrine\ORM\EntityRepository;

class MembreRepository extends EntityRepository
{

    /**
     * Retourne le tableau des $limit premiers joueurs
     *
     * @param int $limit
     * @return array
     */
	public function getClassementGeneral($type, int $limit){
		$query = $this->createQueryBuilder('m')
            ->select('m.id, m.username, m.dateInscription')
			->leftJoin("m.phrases", "p")->addSelect('count(distinct p.id) as nbPhrases')
			->leftJoin("p.jAime", "lp", 'with', 'lp.active = 1')->addSelect('count(distinct lp.id) as nbJAime')
			->groupBy('m.id')
			->setMaxResults($limit);

		if ($type == 'mensuel') {
            $query
                ->addSelect('m.pointsClassementMensuel pointsClassement')
                ->where("m.pointsClassementMensuel > 0")
                ->orderBy('m.pointsClassementMensuel', 'DESC');
        }
        elseif ($type == 'hebdomadaire') {
            $query
                ->addSelect('m.pointsClassementHebdomadaire pointsClassement')
                ->where("m.pointsClassementHebdomadaire > 0")
                ->orderBy('m.pointsClassementHebdomadaire', 'DESC');
        }
        else {
            $query
                ->addSelect('m.pointsClassement')
                ->where("m.pointsClassement > 0")
                ->orderBy('m.pointsClassement', 'DESC');
        }

		return $query->getQuery()->getResult();
	}

    /**
     *
     *
     * @param Membre $membre
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function getPositionClassement($type, Membre $membre)
	{
		$query =  $this->createQueryBuilder('m')
			->select('count(m) position');

        if ($type == 'mensuel') {
            $query
                ->where("m.pointsClassementMensuel > :points")->setParameter("points", $membre->getPointsClassementMensuel())
                ->orderBy('m.pointsClassementMensuel', 'DESC');
        }
        elseif ($type == 'hebdomadaire') {
            $query
                ->where("m.pointsClassementHebdomadaire > :points")->setParameter("points", $membre->getPointsClassementHebdomadaire())
                ->orderBy('m.pointsClassementHebdomadaire', 'DESC');
        }
        else {
            $query
                ->where("m.pointsClassement > :points")->setParameter("points", $membre->getPointsClassement())
			    ->orderBy('m.pointsClassement', 'DESC');
        }

			return $query->getQuery()->getOneOrNullResult()['position'] + 1;
	}

    /**
     * Retourne le nombre de membres activés
     *
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function countEnabled()
	{
		return $this->createQueryBuilder('m')
			->select('count(m) total')
            ->where('m.enabled = 1')
			->getQuery()->getSingleResult()['total'];
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

		$array['total'] = $this->createQueryBuilder('m')
			                  ->select('count(m) total')
			                  ->getQuery()->getSingleResult()['total'];

		$dateJ30 = new \DateTime();
		$dateJ30->setTimestamp($dateJ30->getTimestamp() - (3600 * 24 * 30));
		$array['inscriptionJ30'] = $this->createQueryBuilder('m')
			                           ->select('count(m) inscriptionJ30')
			                           ->where('m.dateInscription > :j30')->setParameter('j30', $dateJ30)
			                           ->getQuery()->getSingleResult()['inscriptionJ30'];

		$dateJ7 = new \DateTime();
		$dateJ7->setTimestamp($dateJ7->getTimestamp() - (3600 * 24 * 7));
		$array['inscriptionJ7'] = $this->createQueryBuilder('m')
			                          ->select('count(m) inscriptionJ7')
			                          ->where('m.dateInscription > :j7')->setParameter('j7', $dateJ7)
			                          ->getQuery()->getSingleResult()['inscriptionJ7'];

		$dateH24 = new \DateTime();
		$dateH24->setTimestamp($dateH24->getTimestamp() - (3600 * 24));
		$array['inscriptionH24'] = $this->createQueryBuilder('m')
			                           ->select('count(m) inscriptionH24')
			                           ->where('m.dateInscription > :h24')->setParameter('h24', $dateH24)
			                           ->getQuery()->getSingleResult()['inscriptionH24'];

		$array['bannis'] = $this->createQueryBuilder('m')
			                   ->select('count(m) bannis')
			                   ->where('m.banni = 1')
			                   ->getQuery()->getSingleResult()['bannis'];

		$array['inactifs'] = $this->createQueryBuilder('m')
			                     ->select('count(m) inactifs')
			                     ->where('m.enabled = 0')
			                     ->getQuery()->getSingleResult()['inactifs'];

		$array['newsletter'] = $this->createQueryBuilder('m')
			                       ->select('count(m) newsletter')
			                       ->where('m.newsletter = 1')
			                       ->getQuery()->getSingleResult()['newsletter'];

        $array['service'] = $this->createQueryBuilder('m')
            ->select('count(m) service')
            ->where('m.facebookId is not null')
            ->orWhere('m.twitterId is not null')
            ->orWhere('m.googleId is not null')
            ->getQuery()->getSingleResult()['service'];

		$array['homme'] = $this->createQueryBuilder('m')
			                  ->select('count(m) homme')
			                  ->where('m.sexe = :sexe')->setParameter('sexe', 'Homme')
			                  ->getQuery()->getSingleResult()['homme'];

		$array['femme'] = $this->createQueryBuilder('m')
			                  ->select('count(m) femme')
			                  ->where('m.sexe = :sexe')->setParameter('sexe', 'Femme')
			                  ->getQuery()->getSingleResult()['femme'];

		$array['moyPoints'] = $this->createQueryBuilder('m')
			                      ->select('avg(m.pointsClassement) moyPoints')
			                      ->getQuery()->getSingleResult()['moyPoints'];

		$array['moyCredits'] = $this->createQueryBuilder('m')
			                       ->select('avg(m.credits) moyCredits')
			                       ->getQuery()->getSingleResult()['moyCredits'];

		return $array;
	}

    /**
     * Retourne le nombre de membres signalés
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countSignale()
    {
        return $this->createQueryBuilder('m')
            ->select('count(m) signale')
            ->where('m.signale = 1')
            ->getQuery()->getSingleResult()['signale'];
    }

    /**
     * Retourne le nombre de points nécessaire pour passer devant le prochain joueur aux classements
     *
     * @param Membre $membre
     * @return array Tableau des prochains membres pour chaque classement
     */
    public function getNextMembresClassements(Membre $membre)
    {
        $array = array();

        $array['gen'] = $this->createQueryBuilder('m')
             ->where('m.pointsClassement > :points')->setParameter('points', $membre->getPointsClassement())
             ->orderBy('m.pointsClassement', 'ASC')
             ->setMaxResults(1)
             ->getQuery()->getOneOrNullResult();

        $array['men'] = $this->createQueryBuilder('m')
            ->where('m.pointsClassementMensuel > :points')->setParameter('points', $membre->getPointsClassementMensuel())
            ->orderBy('m.pointsClassementMensuel', 'ASC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        $array['heb'] = $this->createQueryBuilder('m')
            ->where('m.pointsClassementHebdomadaire > :points')->setParameter('points', $membre->getPointsClassementHebdomadaire())
            ->orderBy('m.pointsClassementHebdomadaire', 'ASC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        return $array;
    }

    /**
     * Remet à zéro les points de classement hebdomadaire
     *
     * @return mixed Nombre de lignes modifiées
     */
    public function resetPointsHebdomadaire()
    {
        $query = $this->createQueryBuilder('m')
            ->update()
            ->set('m.pointsClassementHebdomadaire', 0)
            ->where('m.pointsClassementHebdomadaire != 0');

        return $query->getQuery()->execute();
    }

    /**
     * Remet à zéro les points de classement mensuel
     *
     * @return mixed Nombre de lignes modifiées
     */
    public function resetPointsMensuel()
    {
        $query = $this->createQueryBuilder('m')
            ->update()
            ->set('m.pointsClassementMensuel', 0)
            ->where('m.pointsClassementMensuel != 0');

        return $query->getQuery()->execute();
    }

    /**
     * Retourne un tableau de membre trié par odre décroissant du nombre de points hebdomadaire
     *
     * @return mixed Tableau de membre
     */
    public function getAllWithPointsHebdomadaire()
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.pointsClassementHebdomadaire > 0')
            ->orderBy('m.pointsClassementHebdomadaire', 'DESC');

        return $query->getQuery()->getResult();
    }

    /**
     * Retourne un tableau de membre trié par odre décroissant du nombre de points mensuel
     *
     * @return mixed Tableau de membre
     */
    public function getAllWithPointsMensuel()
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.pointsClassementMensuel > 0')
            ->orderBy('m.pointsClassementMensuel', 'DESC');

        return $query->getQuery()->getResult();
    }

}
