<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Util\User;

class AdminControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testDisconnected()
    {
        // Suit les redirections
        $this->client->followRedirects();

        // Non connecté
        $crawler = $this->client->request('GET', '/admin');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('title:contains("Connexion")')->count());
    }

    public function testProfilsAccessDenied()
    {
        // Suit les redirections
        $this->client->followRedirects();

        // Membre
        User::logIn($this->client, static::$kernel, User::$MEMBRE);
        $this->client->request('GET', '/admin');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

        // Modérateur
        User::logIn($this->client, static::$kernel, User::$MODO);
        $this->client->request('GET', '/admin');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminAccessGranted()
    {
        // Suit les redirections
        $this->client->followRedirects();

        // Administrateur
        User::logIn($this->client, static::$kernel, User::$ADMIN);
        $this->client->request('GET', '/admin');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

}
