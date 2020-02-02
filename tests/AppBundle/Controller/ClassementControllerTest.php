<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ClassementControllerTest extends WebTestCase
{
    public function testShowClassementGeneral()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/classement/joueurs');

        // Suit les redirections (pour le sous menu)
        $client->followRedirects();

        // Le sous menu est présent
        $this->assertGreaterThan(0, $crawler->filter('div.well ul.nav')->count());
        // Le tableau est présent
        $this->assertGreaterThan(0, $crawler->filter('#classement')->count());

        // Clic sur le lien du classement général
        $link = $crawler->selectLink('Général')->link();
        $client->click($link);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains('/classement/joueurs', $client->getRequest()->getUri());

        // Clic sur le lien du classement mensuel
        $link = $crawler->selectLink('Mensuel')->link();
        $client->click($link);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains('/classement/joueurs?type=mensuel', $client->getRequest()->getUri());

        // Clic sur le lien du classement hebdomadaire
        $link = $crawler->selectLink('Hebdomadaire')->link();
        $client->click($link);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains('/classement/joueurs?type=hebdomadaire', $client->getRequest()->getUri());
    }

}
