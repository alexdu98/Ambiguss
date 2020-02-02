<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Phrase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhraseTest extends KernelTestCase
{
    private $container;
    private $entity;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->container = $kernel->getContainer();
        $this->entity = new Phrase();
    }

    public function testUpdateGainCreateur()
    {
        $this->entity->setGainCreateur(0);

        $this->entity->updateGainCreateur(0);
        $this->assertEquals($this->entity->getGainCreateur(), 0);

        $this->entity->updateGainCreateur(100);
        $this->assertEquals($this->entity->getGainCreateur(), 100);

        $this->entity->updateGainCreateur(100);
        $this->assertEquals($this->entity->getGainCreateur(), 200);

        $this->entity->updateGainCreateur(-150);
        $this->assertEquals($this->entity->getGainCreateur(), 50);

        $this->entity->updateGainCreateur(-100);
        $this->assertEquals($this->entity->getGainCreateur(), 0);
    }

    public function testGetContenuAmb()
    {
        $this->entity->setContenu('<amb id="1">amb</amb>');
        $this->assertEquals('<amb>amb</amb>', $this->entity->getContenuAmb());

        $this->entity->setContenu('<amb id="1">amb</amb> <amb id="2">amb</amb>');
        $this->assertEquals('<amb>amb</amb> <amb>amb</amb>', $this->entity->getContenuAmb());

        $this->entity->setContenu('Un mot <amb id="1">amb</amb> puis un autre <amb id="2">amb</amb>.');
        $this->assertEquals('Un mot <amb>amb</amb> puis un autre <amb>amb</amb>.', $this->entity->getContenuAmb());
    }

    public function testGetContenuHTML()
    {
        $this->entity->setContenu('<amb id="1">amb</amb>');
        $expected = '<amb id="ma1" class="ma color-red" title="Ce mot est ambigu (id : 1)">amb</amb>';
        $this->assertEquals($expected, $this->entity->getContenuHTML());

        $this->entity->setContenu('<amb id="1">amb</amb> <amb id="2">amb</amb>');
        $expected = '<amb id="ma1" class="ma color-red" title="Ce mot est ambigu (id : 1)">amb</amb> <amb id="ma2" class="ma color-red" title="Ce mot est ambigu (id : 2)">amb</amb>';
        $this->assertEquals($expected, $this->entity->getContenuHTML());

        $this->entity->setContenu('Un mot <amb id="1">amb</amb> puis un autre <amb id="2">amb</amb>.');
        $expected = 'Un mot <amb id="ma1" class="ma color-red" title="Ce mot est ambigu (id : 1)">amb</amb> puis un autre <amb id="ma2" class="ma color-red" title="Ce mot est ambigu (id : 2)">amb</amb>.';
        $this->assertEquals($expected, $this->entity->getContenuHTML());
    }

    public function testNormalize()
    {
        $this->entity->setContenu('perfect');
        $this->entity->normalize();
        $this->assertEquals('Perfect.', $this->entity->getContenu());

        $this->entity->setContenu('àççént');
        $this->entity->normalize();
        $this->assertEquals('àççént.', $this->entity->getContenu());

        $this->entity->setContenu(' espace');
        $this->entity->normalize();
        $this->assertEquals('Espace.', $this->entity->getContenu());

        $this->entity->setContenu('espace ');
        $this->entity->normalize();
        $this->assertEquals('Espace.', $this->entity->getContenu());

        $this->entity->setContenu('  espace');
        $this->entity->normalize();
        $this->assertEquals('Espace.', $this->entity->getContenu());

        $this->entity->setContenu('espace  ');
        $this->entity->normalize();
        $this->assertEquals('Espace.', $this->entity->getContenu());

        $this->entity->setContenu('plusieurs mots !');
        $this->entity->normalize();
        $this->assertEquals('Plusieurs mots !', $this->entity->getContenu());

        $this->entity->setContenu('plusieurs  mots ?');
        $this->entity->normalize();
        $this->assertEquals('Plusieurs mots ?', $this->entity->getContenu());
    }

    public function testIsValid()
    {
        $this->entity->setContenu('<b>test</b>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('que des balises <amb>', $this->entity->isValid()['message']);

        $this->entity->setContenu('<amb id="1">test<amb id="2">test</amb>test</amb>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('pas de balise <amb> imbriquée', $this->entity->isValid()['message']);

        $this->entity->setContenu('<amb>test</amb> <amb>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('même nombre de balise <amb>', $this->entity->isValid()['message']);

        $this->entity->setContenu('</amb> <amb>test</amb>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('même nombre de balise <amb>', $this->entity->isValid()['message']);

        $this->entity->setContenu('test');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('au moins 1 mot ambigu', $this->entity->isValid()['message']);

        $this->entity->setContenu('<amb id="1">1</amb><amb id="2">2</amb><amb id="3">3</amb><amb id="4">4</amb><amb id="5">5</amb><amb id="6">6</amb><amb id="7">7</amb><amb id="8">8</amb><amb id="9">9</amb><amb id="10">10</amb><amb id="11">11</amb>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('pas dépasser 10 mots ambigus', $this->entity->isValid()['message']);

        $this->entity->setContenu('<amb id="1">1</amb> test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test test');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('255 caractères maximum hors balise <amb>', $this->entity->isValid()['message']);

        $this->entity->setContenu('a<amb id="1">1</amb>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('Un mot était mal sélectionné (le caractère précédent une balise <amb> ou suivant une balise </amb> ne doit pas être alphabétique)', $this->entity->isValid()['message']);

        $this->entity->setContenu('<amb id="1">1</amb>a');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('Un mot était mal sélectionné (le caractère précédent une balise <amb> ou suivant une balise </amb> ne doit pas être alphabétique)', $this->entity->isValid()['message']);

        $this->entity->setContenu('a<amb id="1">1</amb>a');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('Un mot était mal sélectionné (le caractère précédent une balise <amb> ou suivant une balise </amb> ne doit pas être alphabétique)', $this->entity->isValid()['message']);

        $this->entity->setContenu('<amb id="1"> 1</amb>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('le caractère suivant une balise <amb> ou précédent une balise </amb> ne doit pas être un espace', $this->entity->isValid()['message']);

        $this->entity->setContenu('<amb id="1">1 </amb>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('le caractère suivant une balise <amb> ou précédent une balise </amb> ne doit pas être un espace', $this->entity->isValid()['message']);

        $this->entity->setContenu('<amb id="1"> 1 </amb>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('le caractère suivant une balise <amb> ou précédent une balise </amb> ne doit pas être un espace', $this->entity->isValid()['message']);

        $this->entity->setContenu('<amb id="1">1</amb> <amb id="1">1</amb>');
        $this->assertFalse($this->entity->isValid()['succes']);
        $this->assertContains('Les mots ambigus doivent avoir des identifiants différents', $this->entity->isValid()['message']);

        $this->entity->setContenu('Avant <amb id="2">2</amb>');
        $this->assertTrue($this->entity->isValid()['succes']);
        $this->assertNotEmpty($this->entity->isValid()['motsAmbigus']);
        $this->assertEquals('Avant <amb id="1">2</amb>', $this->entity->getContenu());

        $this->entity->setContenu('<amb id="2">2</amb> après.');
        $this->assertTrue($this->entity->isValid()['succes']);
        $this->assertNotEmpty($this->entity->isValid()['motsAmbigus']);
        $this->assertEquals('<amb id="1">2</amb> après.', $this->entity->getContenu());

        $this->entity->setContenu('Avant <amb id="2">2</amb> après.');
        $this->assertTrue($this->entity->isValid()['succes']);
        $this->assertNotEmpty($this->entity->isValid()['motsAmbigus']);
        $this->assertEquals('Avant <amb id="1">2</amb> après.', $this->entity->getContenu());

        $this->entity->setContenu('Avant <amb id="2">2</amb> test à@&é <amb id="1">1</amb> après.');
        $this->assertTrue($this->entity->isValid()['succes']);
        $this->assertNotEmpty($this->entity->isValid()['motsAmbigus']);
        $this->assertEquals('Avant <amb id="1">2</amb> test à@&é <amb id="2">1</amb> après.', $this->entity->getContenu());
    }

}
