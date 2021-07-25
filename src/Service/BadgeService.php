<?php

namespace App\Service;

use App\Entity\Badge;
use App\Entity\Membre;
use App\Entity\MembreBadge;
use App\Event\GameEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class BadgeService
{
    private $container;
    private $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
    }

    public function check(Membre $membre, $type)
    {
        $badgeRepo = $this->em->getRepository('App:Badge');
        $partieRepo = $this->em->getRepository('App:Partie');
        $phraseRepo = $this->em->getRepository('App:Phrase');
        $membreRepo = $this->em->getRepository('App:Membre');

        /** @var Badge[] $badges */
        $badges = $badgeRepo->getNotWinYetForMembreAndType($membre, $type);

        switch ($type) {
            case 'JOUER_PARTIE_TOTAL':
                $nbParties = $partieRepo->count(array('joueur' => $membre));

                foreach ($badges as $badge) {
                    if ($nbParties >= $badge->getNombre()) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'JOUER_PARTIE_1_JOUR':
                // Récupération du nombre de phrases jouées du membre par jour
                $nbPartiesByDay = $partieRepo->getByDayForMembre($membre);

                // Pour chacun des badges du type donné
                foreach ($badges as $badge) {
                    if ($this->hasGroupWithEqualOrMore($nbPartiesByDay, $badge->getNombre())) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'JOUER_PARTIE_3_JOURS':
                // Récupération du nombre de phrases jouées du membre par jour
                $nbPartiesByDay = $partieRepo->getByDayForMembre($membre);

                // Pour chacun des badges du type donné
                foreach ($badges as $badge) {
                    if ($this->hasSuiteDayWithEqualOrMore($nbPartiesByDay, $badge->getNombre(), 3)) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'JOUER_PARTIE_7_JOURS':
                // Récupération du nombre de phrases jouées du membre par jour
                $nbPartiesByDay = $partieRepo->getByDayForMembre($membre);

                // Pour chacun des badges du type donné
                foreach ($badges as $badge) {
                    if ($this->hasSuiteDayWithEqualOrMore($nbPartiesByDay, $badge->getNombre(), 7)) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'CREER_PHRASE_TOTAL':
                $nbPhrases = $phraseRepo->count(array('auteur' => $membre));

                foreach ($badges as $badge) {
                    if ($nbPhrases >= $badge->getNombre()) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'CREER_PHRASE_1_JOUR':
                // Récupération du nombre de phrases jouées du membre par jour
                $nbPhrasesByDay = $phraseRepo->getByDayForMembre($membre);

                // Pour chacun des badges du type donné
                foreach ($badges as $badge) {
                    if ($this->hasGroupWithEqualOrMore($nbPhrasesByDay, $badge->getNombre())) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'CREER_PHRASE_3_JOURS':
                // Récupération du nombre de phrases jouées du membre par jour
                $nbPhrasesByDay = $phraseRepo->getByDayForMembre($membre);

                // Pour chacun des badges du type donné
                foreach ($badges as $badge) {
                    if ($this->hasSuiteDayWithEqualOrMore($nbPhrasesByDay, $badge->getNombre(), 3)) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'CREER_PHRASE_7_JOURS':
                // Récupération du nombre de phrases jouées du membre par jour
                $nbPhrasesByDay = $phraseRepo->getByDayForMembre($membre);

                // Pour chacun des badges du type donné
                foreach ($badges as $badge) {
                    if ($this->hasSuiteDayWithEqualOrMore($nbPhrasesByDay, $badge->getNombre(), 7)) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'RECEVOIR_JAIME_TOTAL':
                $nbJAime = $membreRepo->countJAimeRecu($membre);

                foreach ($badges as $badge) {
                    if ($nbJAime >= $badge->getNombre()) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'RECEVOIR_JAIME_1_PHRASE':
                $classementPhrases = $phraseRepo->getClassementPhrasesUser($membre);

                foreach ($badges as $badge) {
                    if ($this->hasGroupWithEqualOrMore($classementPhrases, $badge->getNombre(), 'nbJAime')) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'SIGNALEMENT_VALIDE_TOTAL':
                $signalementRepo = $this->em->getRepository('App:Signalement');
                $repoTV = $this->em->getRepository('App:TypeVote');

                $verdict = $repoTV->findOneBy(array('nom' => 'Valide'));
                $nbSignalementsValides = $signalementRepo->count(array(
                    'auteur' => $membre,
                    'verdict' => $verdict
                ));

                foreach ($badges as $badge) {
                    if ($nbSignalementsValides >= $badge->getNombre()) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'CLASSEMENT_GEN':
                $positionClassement = $membreRepo->getPositionClassement('général', $membre);

                foreach ($badges as $badge) {
                    if ($positionClassement <= $badge->getNombre()) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'CLASSEMENT_HEBDO':
                $positionClassement = $membreRepo->getPositionClassement('hebdomadaire', $membre);

                foreach ($badges as $badge) {
                    if ($positionClassement <= $badge->getNombre()) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;

            case 'CLASSEMENT_MEN':
                $positionClassement = $membreRepo->getPositionClassement('mensuel', $membre);

                foreach ($badges as $badge) {
                    if ($positionClassement <= $badge->getNombre()) {
                        $this->addBadge($membre, $badge, $type);
                    }
                    else {
                        break;
                    }
                }

                break;
        }
    }

    /**
     * Ajoute un badge à un membre en mettant à jour ses points et son historique et en affichant une notification si nécessaire
     *
     * @param Membre $membre
     * @param Badge $badge
     * @param String $type
     * @param \DateTime|null $date
     * @throws \Exception
     */
    private function addBadge(Membre $membre, Badge $badge, string $type, \DateTime $date = null)
    {
        $notifier = $this->container->get('App\Service\NotifyService');
        $historizer = $this->container->get('App\Service\HistoriqueService');
        $logger = $this->container->get('logger');

        $logger->info(
            'Obtention du badge « ' . $badge->getDescription() . ' »',
            array(
                'membre' => $membre->getUsernameCanonical(),
                'badge' => $badge->getDescription()
            )
        );

        $membreBadge = new MembreBadge();
        $membreBadge->setBadge($badge);
        $membreBadge->setMembre($membre);
        $membreBadge->setDateObtention($date ?? new \DateTime());

        $this->em->persist($membreBadge);
        $notifier->addWinBadge($membreBadge);

        $membre->updateCredits($badge->getPoints());
        $membre->updatePoints($badge->getPoints());
        $this->em->persist($membre);

        $historizer->save(
            $membreBadge->getMembre(),
            'Vous avez gagné le badge : « ' . $membreBadge->getBadge()->getDescription() . ' » (+' . $membreBadge->getBadge()->getPoints() . ' crédits/points).'
        );

        $this->em->flush();

        // Lance l'événement de mise à jour des points si ce n'est pas déjà l'événement en cours
        if ($type != 'CLASSEMENT_GEN') {
            $ed = $this->container->get('event_dispatcher');
            $event = new GenericEvent(GameEvents::POINTS_GAGNES, array(
                'membre' => $membre,
            ));
            $ed->dispatch(GameEvents::POINTS_GAGNES, $event);
        }
    }

    /**
     * Retourne true si le tableau $nbCountByGroup contient un champ $nomCle >= à $nombre
     *
     * @param $nbCountByGroup
     * @param $nombre
     * @return bool
     */
    private function hasGroupWithEqualOrMore($nbCountByGroup, $nombre, $nomCle = 'count')
    {
        // Si $nbCountByGroup est un tableau et que l'on trouve une valeur de count >= à $nombre, retourn true
        return is_array($nbCountByGroup) && !empty(array_filter($nbCountByGroup, function($oneGroup) use ($nombre, $nomCle) {
            return $oneGroup[$nomCle] >= $nombre;
        }));
    }

    /**
     * Retourne true si le tableau $nbCountByDay contient un count >= à $nombre pendant au moins $nbJourSuite jours
     *
     * @param $nbCountByDay
     * @param $nombre
     * @param $nbJourSuite
     * @return bool
     */
    private function hasSuiteDayWithEqualOrMore($nbCountByDay, $nombre, $nbJourSuite)
    {
        // Si $nbCountByDay n'est pas un tableau ou qu'il y a moins de jours que $nbJourSuite
        if (!is_array($nbCountByDay) || count($nbCountByDay) < $nbJourSuite) {
            return false;
        }

        $nbJoursSuite = 1;
        $datePrec = null;
        $nbCountSuite = array();
        // Pour chaque jour
        foreach ($nbCountByDay as $key => $oneDay) {
            $date = \DateTime::createFromFormat('Y-m-d', $oneDay['date']);

            // S'il y a au moins un jour précédent
            if ($key >= 1) {
                // Calcul la différence de jour
                $interval = $date->diff($datePrec);

                // S'il n'y a qu'un seul jour de différence
                if ($interval->format('%R%a') == '-1') {
                    $nbJoursSuite++;

                    // S'il y a plus de jours consécutifs que nécessaire il faut supprimer le plus anciens count
                    if ($nbJoursSuite > $nbJourSuite) {
                        array_shift($nbCountSuite);
                    }

                    // Ajout du count dans le tableau de la suite
                    array_push($nbCountSuite, $oneDay['count']);

                    // S'il y a $nbJourSuite à la suite avec plus de phrases jouées que le nombre requis
                    if ($nbJoursSuite >= $nbJourSuite && min($nbCountSuite) >= $nombre) {
                        return true;
                    }
                }
                // Si les jours ne sont pas consécutifs, remise à zéro
                else {
                    $nbJoursSuite = 1;
                    $nbCountSuite = array();
                }
            }
            else {
                array_push($nbCountSuite, $oneDay['count']);
            }

            $datePrec = $date;
        }

        return false;
    }

}
