<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class LockedException extends AccountStatusException
{
    public function getMessageKey()
    {
        return $this->getMessage();
    }
}
