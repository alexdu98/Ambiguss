<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Util\User;

class MainControllerTest extends WebTestCase
{
    private $client = null;
    private $user = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testShowAccueil()
    {
        $crawler = $this->client->request('GET', '/');

        // La page renvoie un code 200
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Le titre est présent et contient...
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Bienvenue sur Ambiguss")')->count());
    }

    public function testShowMentions()
    {
        $crawler = $this->client->request('GET', '/mentions');

        // La page renvoie un code 200
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Le titre est présent et contient...
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Mentions légales")')->count());
    }

    public function testShowConditions()
    {
        $crawler = $this->client->request('GET', '/conditions');

        // La page renvoie un code 200
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Le titre est présent et contient...
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Conditions d\'utilisation")')->count());
    }

    public function testShowAPropos()
    {
        $crawler = $this->client->request('GET', '/a-propos');

        // La page renvoie un code 200
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Le titre est présent et contient...
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("À propos")')->count());
    }

    public function testShowExport()
    {
        $crawler = $this->client->request('GET', '/export');

        // La page renvoie un code 200
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Le titre est présent et contient...
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Export")')->count());
    }

    public function testShowContactDisconnected()
    {
        $crawler = $this->client->request('GET', '/contact');

        // La page renvoie un code 200
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Le titre est présent et contient...
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Contact")')->count());

        // Le bouton valider est présent
        $this->assertGreaterThan(0, $crawler->filter('button#AppBundle_contact_envoyer')->count());
    }

    public function testShowContactConnected()
    {
        $this->user = User::logIn($this->client, static::$kernel, User::$MEMBRE);

        $crawler = $this->client->request('GET', '/contact');

        // La page renvoie un code 200
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Le titre est présent et contient...
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Contact")')->count());

        // Le bouton valider est présent
        $this->assertGreaterThan(0, $crawler->filter('button#AppBundle_contact_envoyer')->count());
    }

}
