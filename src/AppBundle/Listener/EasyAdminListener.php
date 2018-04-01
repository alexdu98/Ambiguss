<?php

namespace AppBundle\Listener;

use AppBundle\Entity\JAime;
use AppBundle\Entity\CategorieJugement;
use AppBundle\Entity\Glose;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Historique;
use AppBundle\Entity\Jugement;
use AppBundle\Entity\Membre;
use AppBundle\Entity\MotAmbigu;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Phrase;
use AppBundle\Entity\Reponse;
use AppBundle\Entity\Role;
use AppBundle\Entity\TypeObjet;
use AppBundle\Entity\TypeVote;
use AppBundle\Entity\Visite;
use AppBundle\Entity\Vote;
use AppBundle\Service\HistoriqueService;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EasyAdminListener implements EventSubscriberInterface
{

    private $historiqueService;
    private $user;

    public function __construct(HistoriqueService $historiqueService, TokenStorageInterface $tokenStorage)
    {
        $this->historiqueService = $historiqueService;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::POST_UPDATE => 'update',
            EasyAdminEvents::POST_PERSIST => 'persist',
            EasyAdminEvents::POST_REMOVE => 'remove'
        ];
    }

    public function update(GenericEvent $entity){
        $this->historiqueService->save($this->user, $this->getMessage('Modification', $entity));
    }

    public function persist(GenericEvent $entity){
        $this->historiqueService->save($this->user, $this->getMessage('Création', $entity));
    }

    public function remove(GenericEvent $entity){
        $this->historiqueService->save($this->user, $this->getMessage('Suppression', $entity));
    }

    private function getMessage($type, GenericEvent $entity){
        $classe = $entity->getSubject();

        $articleEntite = null;
        // getShortName() : AppBundle\Entity\MotAmbigu => MotAmbigu
        // MotAmbigu => mot ambigu
        $entityName = trim(mb_strtolower(implode(' ', preg_split('/(?=[A-Z])/', (new \ReflectionClass($classe))->getShortName()))));
        switch($classe){
            case $classe instanceof Membre:
            case $classe instanceof Groupe:
            case $classe instanceof Role:
            case $classe instanceof Jugement:
            case $classe instanceof MotAmbigu:
            case $classe instanceof TypeObjet:
            case $classe instanceof TypeVote:
            case $classe instanceof Vote:
            $articleEntite = "du {$entityName}";
                break;

            case $classe instanceof Historique:
                $articleEntite = "de l'{$entityName}";
                break;

            case $classe instanceof Phrase:
            case $classe instanceof Glose:
            case $classe instanceof Partie:
            case $classe instanceof Reponse:
            case $classe instanceof Visite:
            case $classe instanceof Newsletter:
            case $classe instanceof CategorieJugement:
                $articleEntite = "de la {$entityName}";
                break;

            case $classe instanceof JAime:
                $articleEntite = "du j'aime";
                break;
        }

        // L'entité n'a plus d'id après le DELETE
        if($type == 'Suppression')
            $id = $entity->getArgument('request')->query->get('id');
        else
            $id = $classe->getId();

        $message = "[admin] {$type} {$articleEntite} #{$id}";

        return $message;
    }
}
