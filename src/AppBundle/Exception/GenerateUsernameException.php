<?php

namespace AppBundle\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class GenerateUsernameException extends AccountStatusException
{
    public function getMessageKey()
    {
        return $this->getMessage();
    }
}
