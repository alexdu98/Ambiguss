<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Glose;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GloseTest extends KernelTestCase
{
    private $container;
    private $entity;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->container = $kernel->getContainer();
        $this->entity = new Glose();
    }

    public function testNormalize()
    {
        $this->entity->setValeur('perfect');
        $this->entity->normalize();
        $this->assertEquals('perfect', $this->entity->getValeur());

        $this->entity->setValeur('MAJ');
        $this->entity->normalize();
        $this->assertEquals('maj', $this->entity->getValeur());

        $this->entity->setValeur('àççént');
        $this->entity->normalize();
        $this->assertEquals('àççént', $this->entity->getValeur());

        $this->entity->setValeur(' espace');
        $this->entity->normalize();
        $this->assertEquals('espace', $this->entity->getValeur());

        $this->entity->setValeur('espace ');
        $this->entity->normalize();
        $this->assertEquals('espace', $this->entity->getValeur());

        $this->entity->setValeur('  espace');
        $this->entity->normalize();
        $this->assertEquals('espace', $this->entity->getValeur());

        $this->entity->setValeur('espace  ');
        $this->entity->normalize();
        $this->assertEquals('espace', $this->entity->getValeur());

        $this->entity->setValeur('plusieurs mots');
        $this->entity->normalize();
        $this->assertEquals('plusieurs mots', $this->entity->getValeur());

        $this->entity->setValeur('plusieurs  mots');
        $this->entity->normalize();
        $this->assertEquals('plusieurs mots', $this->entity->getValeur());

        $this->entity->setValeur('  PLUSIEURS   MOTS AVEC AÇÇENT ');
        $this->entity->normalize();
        $this->assertEquals('plusieurs mots avec aççent', $this->entity->getValeur());
    }

}
