<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class GloseService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function isCreatable($nbGloses, $nbCredits)
    {
        return $nbCredits >= $this->getCostCreate($nbGloses);
    }

    public function getCostCreate($nbGloses)
    {
        // Les X premières gloses d'un mot ambigu sont gratuites
        $nbGlosesPayantes = $nbGloses >= $this->container->getParameter('nbGlosesFree') ? $nbGloses : 0;

        return $nbGlosesPayantes * $this->container->getParameter('costCreateGloseByGlosesOfMotAmbigu');
    }
}
