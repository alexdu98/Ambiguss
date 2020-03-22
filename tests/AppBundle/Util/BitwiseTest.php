<?php

namespace Tests\AppBundle\Util;

use AppBundle\Util\Bitwise;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BitwiseTest extends KernelTestCase
{
    public function testCalcul()
    {
        $this->assertEquals(0, Bitwise::calcul('COOKIE_INFO', []));

        $this->assertEquals(Bitwise::COOKIE_INFO['ambiguss'], Bitwise::calcul('COOKIE_INFO', ['ambiguss']));
        $this->assertEquals(Bitwise::COOKIE_INFO['facebook'], Bitwise::calcul('COOKIE_INFO', ['facebook']));
        $this->assertEquals(Bitwise::COOKIE_INFO['twitter'], Bitwise::calcul('COOKIE_INFO', ['twitter']));
        $this->assertEquals(Bitwise::COOKIE_INFO['google'], Bitwise::calcul('COOKIE_INFO', ['google']));

        $this->assertEquals(Bitwise::COOKIE_INFO['ambiguss'] + Bitwise::COOKIE_INFO['facebook'], Bitwise::calcul('COOKIE_INFO', ['ambiguss', 'facebook']));
        $this->assertEquals(Bitwise::COOKIE_INFO['twitter'] + Bitwise::COOKIE_INFO['google'], Bitwise::calcul('COOKIE_INFO', ['twitter', 'google']));
        $this->assertEquals(Bitwise::COOKIE_INFO['facebook'] + Bitwise::COOKIE_INFO['twitter'], Bitwise::calcul('COOKIE_INFO', ['facebook', 'twitter']));
        $this->assertEquals(Bitwise::COOKIE_INFO['ambiguss'] + Bitwise::COOKIE_INFO['google'], Bitwise::calcul('COOKIE_INFO', ['ambiguss', 'google']));

        $this->assertEquals(
            Bitwise::COOKIE_INFO['ambiguss'] + Bitwise::COOKIE_INFO['facebook'] + Bitwise::COOKIE_INFO['twitter'] + Bitwise::COOKIE_INFO['google'],
            Bitwise::calcul('COOKIE_INFO', ['ambiguss', 'facebook', 'twitter', 'google'])
        );
    }

    public function testSsSet()
    {
        $Bitwise = Bitwise::calcul('COOKIE_INFO', []);
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'ambiguss'));
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'facebook'));
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'twitter'));
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'google'));

        $Bitwise = Bitwise::calcul('COOKIE_INFO', ['ambiguss']);
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'ambiguss'));
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'facebook'));
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'twitter'));
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'google'));

        $Bitwise = Bitwise::calcul('COOKIE_INFO', ['twitter', 'google']);
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'ambiguss'));
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'facebook'));
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'twitter'));
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'google'));

        $Bitwise = Bitwise::calcul('COOKIE_INFO', ['ambiguss', 'twitter', 'google']);
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'ambiguss'));
        $this->assertFalse(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'facebook'));
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'twitter'));
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'google'));

        $Bitwise = Bitwise::calcul('COOKIE_INFO', ['ambiguss', 'facebook', 'twitter', 'google']);
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'ambiguss'));
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'facebook'));
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'twitter'));
        $this->assertTrue(Bitwise::isSet('COOKIE_INFO', $Bitwise, 'google'));
    }
}
