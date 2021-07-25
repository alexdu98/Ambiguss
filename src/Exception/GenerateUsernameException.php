<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Exception levée quand la génération d'un pseudo échoue
 */
class GenerateUsernameException extends AccountStatusException
{
    public function getMessageKey()
    {
        return $this->getMessage();
    }
}
