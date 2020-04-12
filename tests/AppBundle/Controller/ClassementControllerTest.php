<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Util\User;

class ClassementControllerTest extends WebTestCase
{
    private $client = null;
    private $user = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testShowClassementsAndNavigationDisconnected()
    {
        $client = $this->client;

        $crawler = $client->request('GET', '/classement/joueurs');

        // Suit les redirections (pour le sous menu)
        $client->followRedirects();

        // Le sous menu est présent
        $this->assertGreaterThan(0, $crawler->filter('div.well ul.nav')->count());
        // Le tableau est présent
        $this->assertGreaterThan(0, $crawler->filter('#classement-général')->count());

        // Clic sur le lien du classement général
        $link = $crawler->selectLink('Général')->link();
        $client->click($link);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/classement/joueurs', $client->getRequest()->getUri());

        // Clic sur le lien du classement mensuel
        $link = $crawler->selectLink('Mensuel')->link();
        $client->click($link);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/classement/joueurs?type=mensuel', $client->getRequest()->getUri());

        // Clic sur le lien du classement hebdomadaire
        $link = $crawler->selectLink('Hebdomadaire')->link();
        $client->click($link);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/classement/joueurs?type=hebdomadaire', $client->getRequest()->getUri());
    }

    public function testShowClassementsAndNavigationConnected()
    {
        $client = $this->client;
        $this->user = User::logIn($client, static::$kernel, User::$MEMBRE);

        $crawler = $client->request('GET', '/classement/joueurs');

        // Suit les redirections (pour le sous menu)
        $client->followRedirects();

        // Le sous menu est présent
        $this->assertGreaterThan(0, $crawler->filter('div.well ul.nav')->count());
        // Le tableau est présent
        $this->assertGreaterThan(0, $crawler->filter('#classement-général')->count());

        // Clic sur le lien du classement général
        $link = $crawler->selectLink('Général')->link();
        $client->click($link);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/classement/joueurs', $client->getRequest()->getUri());
        $this->assertGreaterThan(0, $crawler->filter('.classementMe')->count());

        // Clic sur le lien du classement mensuel
        $link = $crawler->selectLink('Mensuel')->link();
        $client->click($link);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/classement/joueurs?type=mensuel', $client->getRequest()->getUri());

        // Clic sur le lien du classement hebdomadaire
        $link = $crawler->selectLink('Hebdomadaire')->link();
        $client->click($link);
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/classement/joueurs?type=hebdomadaire', $client->getRequest()->getUri());
    }

}
