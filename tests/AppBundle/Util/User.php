<?php

namespace Tests\AppBundle\Util;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class User
{
    public static $ADMIN = 58;
    public static $MODO = 174;
    public static $MEMBRE = 76;

    public static function logIn(Client $client, KernelInterface $kernel, $login)
    {
        $session = $client->getContainer()->get('session');

        $firewall = 'main';
        $userManager = $kernel->getContainer()->get('doctrine')->getRepository('AppBundle:Membre');

        $user = null;
        if (is_int($login)) {
            $user = $userManager->find($login);
        }
        else {
            $user = $userManager->findUserByEmail($login);
        }

        if (!$user) {
            throw new \Exception('Utilisateur "' . $login . '" inconnu');
        }

        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());

        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $user;
    }
}
