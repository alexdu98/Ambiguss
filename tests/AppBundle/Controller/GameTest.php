<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameTest extends WebTestCase
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
}
