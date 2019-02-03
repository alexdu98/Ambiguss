<?php

namespace AppBundle\Service;

use AppBundle\Entity\Membre;
use AppBundle\Entity\MotAmbigu;
use AppBundle\Entity\MotAmbiguPhrase;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Phrase;
use AppBundle\Entity\Reponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PhraseService
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function new(Phrase $phrase, Membre $auteur, array $mapsRep) {
        $coutUnitaire = $this->container->getParameter('costCreatePhraseByMotAmbiguCredits');
        $gainCreation = $this->container->getParameter('gainCreatePhrasePoints');

        $phrase->setAuteur($auteur);
        $phrase->removeMotsAmbigusPhrase();

        $this->normalize($phrase);
        $res = $this->isValid($phrase);

        $succes = $res['succes'];
        $motsAmbigus = $res['motsAmbigus'];

        if($succes && $auteur->getCredits() < $coutUnitaire * count($motsAmbigus)) {
            $res['succes'] = false;
            $res['message'] = "Vous n'avez pas assez de crédits pour créer une phrase avec " . count($motsAmbigus) . " mots ambigus.";
        }

        if($succes) {
            $this->treatMotsAmbigus($phrase, $auteur, $motsAmbigus);

            // Mise à jour du nombre de crédits et de points de l'auteur
            $auteur->updateCredits(-$coutUnitaire * count($motsAmbigus));
            $auteur->updatePoints($gainCreation);

            $this->em->getConnection()->beginTransaction();
            try
            {
                $this->em->persist($phrase);
                $this->em->flush();

                // On enregistre dans l'historique de l'auteur
                $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');
                $historiqueService->save($auteur, "Création de la phrase n°" . $phrase->getId() . " (+ " . $gainCreation . " points).");

                $this->treatMotsAmbigusPhrase($phrase, $auteur, $motsAmbigus, $mapsRep);

                $partie = new Partie();
                $partie->setJoueur($auteur);
                $partie->setPhrase($phrase);
                $partie->setJoue(true);
                $this->em->persist($partie);

                $this->em->flush();
                $this->em->getConnection()->commit();
            }
            catch(UniqueConstraintViolationException $e)
            {
                $this->em->getConnection()->rollBack();
                $res['succes'] = false;
                $res['message'] = "Erreur lors de l'insertion de la phrase -> la phrase existe déjà.";
            }
            catch(\Exception $e)
            {
                $this->em->getConnection()->rollBack();
                $res['succes'] = false;
                $res['message'] = "Erreur lors de l'insertion de la phrase -> " . $e->getMessage();
            }
        }

        return $res;
    }

    public function treatMotsAmbigusPhrase(Phrase $phrase, Membre $auteur, array $motsAmbigus, array $mapsRep) {
        $repoGlose = $this->em->getRepository('AppBundle:Glose');

        foreach($phrase->getMotsAmbigusPhrase() as $map)
        {
            $rep = new Reponse();
            $rep->setContenuPhrase($phrase->getContenu());
            $rep->setValeurMotAmbigu($map->getMotAmbigu()->getValeur());

            // -1 car l'ordre commence à 1 et le reorder à 0
            $keyForMotsAmbigusPhrase = $motsAmbigus[ $map->getOrdre() - 1 ][1];
            $idGlose = $mapsRep[ $keyForMotsAmbigusPhrase ]['gloses'];
            if(empty($idGlose))
            {
                throw new \Exception("Tous les mots ambigus doivent avoir une glose");
            }
            $glose = $repoGlose->find($idGlose);
            $rep->setValeurGlose($glose->getValeur());
            $rep->setAuteur($auteur);
            $rep->setGlose($glose);
            $rep->setMotAmbiguPhrase($map);
            $rep->setPhrase($phrase);

            $map->addReponse($rep);

            if(!$map->getMotAmbigu()->getGloses()->contains($glose))
            {
                $map->getMotAmbigu()->addGlose($glose);
            }

            $this->em->persist($map);
            $this->em->persist($rep);
        }
    }

    public function treatMotsAmbigus(Phrase $phrase, Membre $auteur, array $motsAmbigus) {
        $repoMA = $this->em->getRepository('AppBundle:MotAmbigu');

        /*
         * $mots_ambigu[0] contient un array du premier match
         * $mots_ambigu[1] contient un array du deuxieme match...
         *
         * $mots_ambigu[][0] contient toute la balise <amb ... </amb>
         * $mots_ambigu[][1] contient l'id / l'ordre du mot ambigu
         * $mots_ambigu[][2] contient le mot ambigu
         */
        foreach($motsAmbigus as $key => $motAmbigu)
        {
            $mot_ambigu_OBJ = new MotAmbigu();
            $mot_ambigu_OBJ->setValeur($motAmbigu[2]);
            $mot_ambigu_OBJ->setAuteur($auteur);

            // Normalise le mot ambigu
            $mot_ambigu_OBJ->normalize();

            $tmp = $repoMA->findOneBy(array('valeur' => $mot_ambigu_OBJ->getValeur()));

            // Soit on le trouve dans la BD soit on l'ajoute
            if($tmp == null){
                $this->em->persist($mot_ambigu_OBJ);
                $this->em->flush();
            }
            else
                $mot_ambigu_OBJ = $tmp;

            $map = new MotAmbiguPhrase();
            $map->setOrdre($key + 1);
            $map->setPhrase($phrase);
            $map->setMotAmbigu($mot_ambigu_OBJ);

            $phrase->addMotAmbiguPhrase($map);
        }
    }

    public function normalize(Phrase $phrase)
    {
        // Supprime les espaces multiples, option u car sinon les caractères utf8 partent en vrille
        $phrase->setContenu(preg_replace('#\s+#u', ' ', $phrase->getContenu()));

        // Met la première lettre en majuscule
        $phrase->setContenu(preg_replace_callback('#^(\<amb id\="[0-9]+"\>)?([a-z])(.*)#', function($matches)
        {
            return $matches[1] . strtoupper($matches[2]) . $matches[3];
        }, $phrase->getContenu()));

        // Ajoute le . final si non existant
        $last_letter = $phrase->getContenu()[ strlen($phrase->getContenu()) - 1 ];

        if($last_letter != '.' && $last_letter != '?' && $last_letter != '!')
        {
            $phrase->setContenu($phrase->getContenu() . '.');
        }
    }

    public function isValid(Phrase $phrase) {
        // Pas d'autres balises html que <amb> et </amb>
        if($phrase->getContenu() != strip_tags($phrase->getContenu(), '<amb>'))
            return array('succes' => false, 'message' => 'Il ne faut que des balises <amb> et </amb>');

        // Le même nombre de balise ouvrante et fermante
        $ambOuv = $ambFer = null;
        $regexOuv = '#\<amb id\="([0-9]+)"\>#';
        $regexFer = '#\</amb\>#';
        preg_match_all($regexOuv, $phrase->getContenu(), $ambOuv, PREG_SET_ORDER);
        preg_match_all($regexFer, $phrase->getContenu(), $ambFer, PREG_SET_ORDER);
        if(count($ambOuv) != count($ambFer))
            return array('succes' => false, 'message' => 'Il n\'y a pas le même nom de balise <amb> et </amb>');

        // récupère les mots ambigus
        $mots_ambigu = array();
        $regex = '#\<amb id\="([0-9]+)"\>(.*?)\</amb\>#'; // Faux bug d'affichage PHPStorm, ne pas toucher
        preg_match_all($regex, $phrase->getContenu(), $mots_ambigu, PREG_SET_ORDER);

        // Au moins 1 mot ambigu
        if(empty($mots_ambigu))
            return array('succes' => false, 'message' => 'Il faut au moins 1 mot ambigu');

        // Pas plus de 10 mots ambigus
        if(count($mots_ambigu) > 10)
            return array(
                'succes' => false,
                'message' => 'Il ne faut pas dépasser 10 mots ambigus par phrase');

        // Pas de balise imbriquée
        foreach($mots_ambigu as $ma){
            if($ma[2] != strip_tags($ma[2]))
                return array('succes' => false, 'message' => 'Il ne faut pas de balise imbriquée');
        }

        // Contenu pur ne dépassent pas 255 caractères
        if(strlen($phrase->getContenuPur()) > 255)
        {
            return array(
                'succes' => false,
                'message' => 'La phrase est trop longue (255 caractères maximum hors balise <amb>)',
            );
        }

        // Mot mal sélectionné
        preg_match_all('#[a-zA-Z]\<amb|amb\>[a-zA-Z]#', $phrase->getContenu(), $arr, PREG_SET_ORDER);
        if(!empty($arr))
        {
            return array(
                'succes' => false,
                'message' => 'Un mot était mal sélectionné (le caractère précédent une balise <amb> ou suivant une balise </amb> ne doit pas être une lettre).',
            );
        }

        // Mot mal sélectionné
        preg_match_all('#\<amb id\="[0-9]+"\> | \</amb\>#', $phrase->getContenu(), $arr, PREG_SET_ORDER);
        if(!empty($arr))
        {
            return array(
                'succes' => false,
                'message' => 'Un mot était mal sélectionné (le caractère suivant une balise <amb> ou précédent une balise </amb> ne doit pas être un espace).',
            );
        }

        // Pas de mot ambigu avec le même id
        $temp = array();
        foreach($mots_ambigu as $ma){
            $temp[$ma[1]] = null;
        }
        if(count($temp) !== count($mots_ambigu))
            return array('succes' => false, 'message' => 'Les mots ambigus doivent avoir des identifiants différents');

        // Réordonne les id
        foreach($mots_ambigu as $key => $ma){
            $regex = '#\<amb id\="' . $ma[1] . '"\>'. $ma[2] .'\</amb\>#';
            $newContenu = preg_replace($regex, '<amb id="' . ($key + 1) . '">' . $ma[2] . '</amb>', $phrase->getContenu());
            $phrase->setContenu($newContenu);
        }

        return array('succes' => true, 'motsAmbigus' => $mots_ambigu);
    }
}
