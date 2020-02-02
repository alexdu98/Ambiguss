<?php

namespace Tests\AppBundle\Twig;

use AppBundle\Twig\PhraseExtension;
use AppBundle\Entity\Phrase;
use PHPUnit\Framework\TestCase;

class PhraseExtensionTest extends TestCase
{
    protected $contenu1 = 'Une phrase <amb id="1">avec</amb> un mot ambigu.';
    protected $attendu1 = 'Une phrase <amb id="ma1" class="ma color-red" title="Ce mot est ambigu (id : 1)">avec</amb> un mot ambigu.';

    protected $contenu2 = 'Une phrase <amb id="1">avec</amb> un <amb id="2">mot</amb> ambigu.';
    protected $attendu2 = 'Une phrase <amb id="ma1" class="ma color-red" title="Ce mot est ambigu (id : 1)">avec</amb> un <amb id="ma2" class="ma color-red" title="Ce mot est ambigu (id : 2)">mot</amb> ambigu.';

    public function testGetStaticHTMLObject()
    {
        $extension = new PhraseExtension();

        $phrase = new Phrase();

        $phrase->setContenu($this->contenu1);
        $res = $extension->getStaticHTML($phrase);
        $this->assertEquals($this->attendu1, $res);

        $phrase->setContenu($this->contenu2);
        $res = $extension->getStaticHTML($phrase);
        $this->assertEquals($this->attendu2, $res);
    }

    public function testGetStaticHTMLString()
    {
        $extension = new PhraseExtension();

        $res = $extension->getStaticHTML($this->contenu1);
        $this->assertEquals($this->attendu1, $res);

        $res = $extension->getStaticHTML($this->contenu2);
        $this->assertEquals($this->attendu2, $res);
    }
}
