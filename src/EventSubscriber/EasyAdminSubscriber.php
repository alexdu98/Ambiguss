<?php

namespace App\EventSubscriber;

use App\Entity\Badge;
use App\Entity\JAime;
use App\Entity\CategorieSignalement;
use App\Entity\Glose;
use App\Entity\Groupe;
use App\Entity\Historique;
use App\Entity\MembreBadge;
use App\Entity\MotAmbiguPhrase;
use App\Entity\Signalement;
use App\Entity\Membre;
use App\Entity\MotAmbigu;
use App\Entity\Partie;
use App\Entity\Phrase;
use App\Entity\Reponse;
use App\Entity\Role;
use App\Entity\TypeObjet;
use App\Entity\TypeVote;
use App\Entity\Visite;
use App\Service\HistoriqueService;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    private $historiqueService;
    private $user;

    public function __construct(HistoriqueService $historiqueService, TokenStorageInterface $tokenStorage)
    {
        $this->historiqueService = $historiqueService;
        if ($tokenStorage->getToken())
            $this->user = $tokenStorage->getToken()->getUser();
    }

    public static function getSubscribedEvents()
    {
        return [
            AfterEntityUpdatedEvent::class => 'update',
            AfterEntityPersistedEvent::class => 'persist',
            AfterEntityDeletedEvent::class => 'remove'
        ];
    }

    /**
     * Enregistre la modification de l'objet dans l'historique de l'administrateur
     *
     * @param GenericEvent $entity
     * @throws \ReflectionException
     */
    public function update(GenericEvent $entity)
    {
        $this->historiqueService->save($this->user, $this->getMessage('Modification', $entity), true);
    }

    /**
     * Enregistre la création de l'objet dans l'historique de l'administrateur
     *
     * @param GenericEvent $entity
     * @throws \ReflectionException
     */
    public function persist(GenericEvent $entity)
    {
        $this->historiqueService->save($this->user, $this->getMessage('Création', $entity), true);
    }


    /**
     * Enregistre la suppression de l'objet dans l'historique de l'administrateur
     *
     * @param GenericEvent $entity
     * @throws \ReflectionException
     */
    public function remove(GenericEvent $entity)
    {
        $this->historiqueService->save($this->user, $this->getMessage('Suppression', $entity), true);
    }

    /**
     * Retourne le message à enregistrer dans l'historique de l'administrateur
     *
     * @param string $type
     * @param GenericEvent $entity
     * @return string
     * @throws \ReflectionException
     */
    private function getMessage(string $type, GenericEvent $entity)
    {
        $classe = $entity->getSubject();

        $articleEntite = null;
        // getShortName() : App\Entity\MotAmbigu => MotAmbigu
        // MotAmbigu => mot ambigu
        $entityName = trim(mb_strtolower(implode(' ', preg_split('/(?=[A-Z])/', (new \ReflectionClass($classe))->getShortName()))));
        switch ($classe) {
            case $classe instanceof Badge:
            case $classe instanceof Membre:
            case $classe instanceof Groupe:
            case $classe instanceof Role:
            case $classe instanceof Signalement:
            case $classe instanceof MotAmbigu:
            case $classe instanceof MotAmbiguPhrase:
            case $classe instanceof TypeObjet:
            case $classe instanceof TypeVote:
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
            case $classe instanceof CategorieSignalement:
                $articleEntite = "de la {$entityName}";
                break;

            case $classe instanceof JAime:
                $articleEntite = "du j'aime";
                break;

            case $classe instanceof MembreBadge:
                $articleEntite = "du badge gagné";
                break;
        }

        // L'entité n'a plus d'id après le DELETE
        if ($type == 'Suppression')
            $id = $entity->getArgument('request')->query->get('id');
        else
            $id = $classe->getId();

        $message = "[admin:{$entity->getArgument('request')->server->get('REMOTE_ADDR')}] {$type} {$articleEntite} #{$id}";

        return $message;
    }

}
