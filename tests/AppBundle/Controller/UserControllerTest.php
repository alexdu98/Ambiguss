<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Util\User;

class UserControllerTest extends WebTestCase
{
    private $client = null;
    private $user = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testShowOwnProfil()
    {
        $this->user = User::logIn($this->client, static::$kernel, User::$MEMBRE);

        $crawler = $this->client->request('GET', '/profil');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Profil', $crawler->filter('h1')->text());

        // Le sous menu est présent
        $this->assertGreaterThan(0, $crawler->filter('#nav-profil')->count());

        // Le tableau est présent
        $this->assertStringContainsString('Historique', $crawler->filter('h3')->text());
        $this->assertGreaterThan(0, $crawler->filter('#historique')->count());

        $this->assertStringContainsString('Parties :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Points :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Crédits :', $crawler->filter('.bulle')->html());

        $this->assertStringContainsString('Date d\'inscription :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Dernière connexion :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Email :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Groupe :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Genre :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Date de naissance :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Newsletter :', $crawler->filter('.bulle')->html());

        // Clic sur le lien du profil
        $link = $crawler->selectLink('Profil')->link();
        $this->client->click($link);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/profil', $this->client->getRequest()->getUri());

        // Clic sur le lien du classement mensuel
        $link = $crawler->selectLink('Modification')->link();
        $this->client->click($link);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/profil/edit', $this->client->getRequest()->getUri());

        // Clic sur le lien du classement hebdomadaire
        $link = $crawler->selectLink('Mot de passe')->link();
        $this->client->click($link);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/profil/edit/password', $this->client->getRequest()->getUri());
    }

    public function testShowPublicProfil()
    {
        $this->user = User::logIn($this->client, static::$kernel, User::$MEMBRE);

        $crawler = $this->client->request('GET', '/profil/' . $this->user->getId());

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Profil', $crawler->filter('h1')->text());
        $this->assertEquals(0, $crawler->filter('h3')->count());

        $this->assertStringContainsString('Parties :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Points :', $crawler->filter('.bulle')->html());
        $this->assertStringNotContainsString('Crédits :', $crawler->filter('.bulle')->html());

        $this->assertStringContainsString('Date d\'inscription :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Dernière connexion :', $crawler->filter('.bulle')->html());
        $this->assertStringNotContainsString('Email :', $crawler->filter('.bulle')->html());
        $this->assertStringContainsString('Groupe :', $crawler->filter('.bulle')->html());
        $this->assertStringNotContainsString('Genre :', $crawler->filter('.bulle')->html());
        $this->assertStringNotContainsString('Date de naissance :', $crawler->filter('.bulle')->html());
        $this->assertStringNotContainsString('Newsletter :', $crawler->filter('.bulle')->html());
    }

    public function testShowAdminLink()
    {
        $this->user = User::logIn($this->client, static::$kernel, User::$ADMIN);

        $crawler = $this->client->request('GET', '/');

        // Suit les redirections (pour la validation du formulaire)
        $this->client->followRedirects();

        // Clic sur le lien du classement général
        $link = $crawler->selectLink('Administration')->link();
        $this->client->click($link);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/admin/', $this->client->getRequest()->getUri());

        // Instanciation de la nouvelle page
        $adminPage = new Crawler($this->client->getResponse()->getContent());

        $this->assertStringContainsString('Accueil Ambiguss', $adminPage->filter('.main-sidebar')->html());
        $this->assertStringContainsString('Statistiques', $adminPage->filter('.main-sidebar')->html());
        $this->assertStringContainsString('Modération', $adminPage->filter('.main-sidebar')->html());
    }

}
