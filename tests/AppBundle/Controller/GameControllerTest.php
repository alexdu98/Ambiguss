<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class GameControllerTest extends WebTestCase
{
    public function testShowGameDisconected()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/jeu');

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

        // Le bouton valider est présent
        $this->assertGreaterThan(0, $crawler->filter('button#AppBundle_game_valider')->count());
    }

    public function testPlayGameDisconected()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/jeu');

        // Suit les redirections (pour la validation du formulaire)
        $client->followRedirects();

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

        // Soumission du formulaire
        $client->submit($form);

        // Instanciation de la nouvelle page
        $afterPlayPage = new Crawler($client->getResponse()->getContent());

        // Redirigé sur la page des résultats
        $this->assertStringContainsString('Résultat', $afterPlayPage->filter('title')->html());
    }
}
