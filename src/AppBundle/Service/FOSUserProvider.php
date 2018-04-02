<?php

namespace AppBundle\Service;

use AppBundle\Entity\Groupe;
use AppBundle\Entity\Historique;
use AppBundle\Entity\Membre;
use AppBundle\Exception\GenerateUsernameException;
use AppBundle\Exception\MailAlreadyUsedException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\Canonicalizer;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUserProvider extends BaseFOSUBProvider
{
    private $em;
    private $session;
    private $services = array(
        'facebook' => array('name' => 'Facebook', 'property' => 'facebookId'),
        'twitter' => array('name' => 'Twitter', 'property' => 'twitterId'),
        'google' => array('name' => 'Google', 'property' => 'googleId')
    );

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $entityManager, SessionInterface $session, array $properties)
    {
        parent::__construct($userManager, $properties);
        $this->em = $entityManager;
        $this->session = $session;
    }

    /**
     * ?
     *
     * @param UserInterface $user
     * @param UserResponseInterface $response
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        die('CONNECT');
    }

    /**
     * Exécuté à chaque connexion
     *
     * @param UserResponseInterface $response
     * @return Membre|\FOS\UserBundle\Model\UserInterface|null|object|UserInterface
     * @throws \ReflectionException
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        // Service utilisé (facebook, twitter, google, etc)
        $service = $this->getProperty($response);

        $userService = $this->em->getRepository(Membre::class)->findOneBy(array(
            $this->services[$service]['property'] => $response->getUsername()
        ));

        $userEmail = $this->em->getRepository(Membre::class)->findOneBy(array(
            'emailCanonical' => (new Canonicalizer())->canonicalize($response->getEmail())
        ));

        $user = null;

        // Si l'utilisateur ne s'est jamais connecté via ce service
        if($userService === null) {

            // Si on ne trouve pas d'utilisateur pour ce mail
            if ($userEmail === null) {
                $user = new Membre();

                // Remplie le nouvel utilisateur
                $this->fillUser($user, $service, $response);

                // On génère un pseudo
                $tryGenerateNumber = 1;
                while(!$this->isUsernameNonUsed($user)){
                    // Si au bout de 100 tentatives on y arrive pas, abandon
                    if($tryGenerateNumber > 100)
                        throw new GenerateUsernameException('Échec de la génération du pseudo.');
                    $user->setUsername($user->getUsername() . substr(uniqid(rand(), true), 0, 5));
                    $tryGenerateNumber++;
                }

                // On prévient l'utilisateur qu'il peut changer de pseudo
                if($tryGenerateNumber > 1)
                    $this->session->getFlashBag()->add('info', 'Vous pouvez modifier le pseudo généré <b>UNE FOIS</b>, en allant sur votre profil.');

                $user->setPlainPassword(bin2hex(openssl_random_pseudo_bytes(20)));

                // On met à jour les infos
                $this->updateServiceInfos($user, $service, $response);

                $this->em->persist($user);
                $this->em->flush();

                $histJoueur = new Historique();
                $histJoueur->setMembre($user);
                $histJoueur->setValeur("Inscription via {$this->services[$service]['name']}.");

                $this->em->persist($histJoueur);
                $this->em->flush();
            }
            else{
                $user = $userEmail;
            }
        }
        // L'utilisateur trouvé avec l'uid du service est le même que celui trouvé avec l'email du service
        elseif($userEmail->getId() == $userService->getId()) {
            $user = $userService;
        }
        else
            throw new MailAlreadyUsedException("L'email de votre compte {$this->services[$service]['name']} ({$response->getEmail()}) est déjà utilisé par {$userEmail->getUsername()}.");

        // On met à jour les infos
        $this->updateServiceInfos($user, $service, $response);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Renseigne le pseudo, ajoute le groupe membre et active le drapeau de création via service
     *
     * @param Membre $user
     * @param $service
     * @param $response
     */
    private function fillUser(Membre $user, $service, $response){
        switch ($service){
            case 'facebook' :
                $user->setUsername($response->getFirstName() . ' ' . $response->getLastName()[0]);
                break;
            case 'twitter' :
                $user->setUsername($response->getNickname());
                break;
            case 'google':
                $user->setUsername($response->getFirstName() . ' ' . $response->getLastName()[0]);
                break;
        }
        $user->setServiceCreation(true);
        $user->addGroup($this->em->getRepository(Groupe::class)->findOneBy(['name' => 'Membre']));
    }

    /**
     * Met à jour l'identifiant et le mail du membre fourni par le service et active le membre
     *
     * @param Membre $user
     * @param $service
     * @param $response
     * @throws \ReflectionException
     */
    private function updateServiceInfos(Membre $user, $service, $response) {
        $method = 'set' . ucfirst($this->services[$service]['property']);
        $reflectionMethod = new ReflectionMethod('AppBundle\Entity\Membre', $method);
        $reflectionMethod->invokeArgs($user, array($response->getUsername()));

        $user->setEmail($response->getEmail());
        $user->setEnabled(true);
    }

    /**
     * Indique si le nom d'utilisateur n'est pas déjà utilisé
     *
     * false si non utilisé, true si utilisé
     *
     * @param Membre $user
     * @return bool
     */
    public function isUsernameNonUsed(Membre $user){
        $userUsername = $this->em->getRepository(Membre::class)->findOneBy(array(
            'usernameCanonical' => (new Canonicalizer())->canonicalize($user->getUsername())
        ));

        if($userUsername != null)
            $user->setRenamable(true);

        return !$userUsername;
    }
}
