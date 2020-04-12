<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Util\User;

class GameControllerTest extends WebTestCase
{
    private $client = null;
    private $user = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testPlayGameDisconnected()
    {
        $crawler = $this->client->request('GET', '/jeu');

        // Suit les redirections (pour la validation du formulaire)
        $this->client->followRedirects();

        $form = $this->checkGame($crawler);

        // Soumission du formulaire
        $this->client->submit($form);

        $this->checkAfterPlay();
    }

    public function testPlayGameConnected()
    {
        $this->user = User::logIn($this->client, static::$kernel, User::$MEMBRE);

        $crawler = $this->client->request('GET', '/jeu');

        // Suit les redirections (pour la validation du formulaire)
        $this->client->followRedirects();

        $form = $this->checkGame($crawler);

        // Soumission du formulaire
        $this->client->submit($form);

        $this->checkAfterPlay();
    }

    private function checkGame(Crawler $crawler)
    {
        // La phrase est présente
        $this->assertGreaterThan(0, $crawler->filter('h3#result')->count());
        // Le formulaire des réponses est présent
        $this->assertGreaterThan(0, $crawler->filter('form#gameForm')->count());

        // Compte le nombre de mots ambigus
        $nbMA = $crawler->filter('h3#result .ma')->count();
        // Compte le nmobre de réponse
        $nbRep = $crawler->filter('form#gameForm .reponseGroupe')->count();
        // Il y a le même nombre de mots ambigus que de réponses
        $this->assertEquals($nbMA, $nbRep);

        // Il y a le même nombre de bouton d'ajout de glose que de mots ambigus
        $nbBtnAddGloseAttendu = $this->user ? $nbRep : 0;
        $nbBtnAddGlose = $crawler->filter('button.addGloseModal')->count();
        $this->assertEquals($nbBtnAddGloseAttendu, $nbBtnAddGlose);

        // Le bouton valider est présent
        $this->assertGreaterThan(0, $crawler->filter('button#AppBundle_game_valider')->count());

        // Récupération du formulaire
        $buttonCrawlerNode = $crawler->selectButton('AppBundle_game_valider');
        $form = $buttonCrawlerNode->form();

        // Récupération des select gloses (réponses)
        $reponsesSelect = $crawler->filter('select.gloses');

        // Séléction des gloses pour chaque réponse
        foreach ($reponsesSelect as $select) {
            $values = $form[$select->getAttribute('name')]->availableOptionValues();
            $form[$select->getAttribute('name')]->select($values[1]);
        }

        return $form;
    }

    private function checkAfterPlay()
    {
        // Instanciation de la nouvelle page
        $afterPlayPage = new Crawler($this->client->getResponse()->getContent());

        // Redirigé sur la page des résultats
        $this->assertStringContainsString('Résultat', $afterPlayPage->filter('title')->html());
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
