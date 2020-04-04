<?php

namespace Tests\AppBundle\Util;

use AppBundle\Entity\Membre;
use PHPUnit\Framework\Warning;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class User
{
    public static $ADMIN = 58;
    public static $MODO = 174;
    public static $MEMBRE = 76;

    public static function logIn(Client $client, KernelInterface $kernel, int $login)
    {
        $session = $client->getContainer()->get('session');

        $firewall = 'main';
        $userManager = $kernel->getContainer()->get('doctrine')->getRepository('AppBundle:Membre');

        /** @var Membre $user */
        $user = $userManager->find($login);

        if (!$user) {
            throw new \Exception('Utilisateur "' . $login . '" inconnu');
        }

        if ($login == self::$MEMBRE && $user->getGroupNames()[0] != 'Membre') {
            throw new Warning('Utilisateur "' . $login . '" non membre');
        }
        elseif ($login == self::$MODO && $user->getGroupNames()[0] != 'Modérateur') {
            throw new Warning('Utilisateur "' . $login . '" non modérateur');
        }
        elseif ($login == self::$ADMIN && $user->getGroupNames()[0] != 'Administrateur') {
            throw new Warning('Utilisateur "' . $login . '" non administrateur');
        }

        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());

        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $user;
    }
}
