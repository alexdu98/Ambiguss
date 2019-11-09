<?php

namespace AppBundle\Service;

use AppBundle\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker extends \Symfony\Component\Security\Core\User\UserChecker
{

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof AdvancedUserInterface) {
            return;
        }

        if (!$user->isAccountNonLocked()) {

            if(!empty($user->getDateDeban())){
                $date = $user->getDateDeban()->format('d/m/Y à H:i');
                $msg = 'Le compte est bloqué jusqu\'au ' . $date . '.';
            }
            else {
                $msg = 'Le compte est définitivement bloqué.';
            }

            $ex = new LockedException($msg);
            throw $ex;
        }

        if (!$user->isEnabled()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);
            throw $ex;
        }

        if (!$user->isAccountNonExpired()) {
            $ex = new AccountExpiredException('User account has expired.');
            $ex->setUser($user);
            throw $ex;
        }
    }

}
