<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Exception levée quand l'adresse email existe déjà dans la base de données
 */
class MailAlreadyUsedException extends AccountStatusException
{
    public function getMessageKey()
    {
        return $this->getMessage();
    }
}
