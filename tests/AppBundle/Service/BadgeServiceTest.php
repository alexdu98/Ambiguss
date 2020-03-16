<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\BadgeService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BadgeServiceTest extends KernelTestCase
{
    public function testHasGroupWithEqualOrMore()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $method = new \ReflectionMethod(BadgeService::class, 'hasGroupWithEqualOrMore');
        $method->setAccessible(true);

        $container = $kernel->getContainer();
        $service = $container->get(BadgeService::class);

        /* ************ */
        /* RETURN FALSE */
        /* ************ */

        $arrayCountByDay = null;
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 1)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 4)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 3),
            array('date' => '2020-01-02', 'count' => 4)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 1),
            array('date' => '2020-01-02', 'count' => 3),
            array('date' => '2020-01-03', 'count' => 4)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5));

        /* *********** */
        /* RETURN TRUE */
        /* *********** */

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 6)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 4)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 4),
            array('date' => '2020-01-02', 'count' => 5)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 5)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 6),
            array('date' => '2020-01-02', 'count' => 4)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 4),
            array('date' => '2020-01-02', 'count' => 6)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 6),
            array('date' => '2020-01-02', 'count' => 6)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 3),
            array('date' => '2020-01-03', 'count' => 4)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 6),
            array('date' => '2020-01-03', 'count' => 3),
            array('date' => '2020-01-05', 'count' => 4)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 1),
            array('date' => '2020-01-02', 'count' => 5),
            array('date' => '2020-01-03', 'count' => 4)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 2),
            array('date' => '2020-01-03', 'count' => 6),
            array('date' => '2020-01-05', 'count' => 4)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 1),
            array('date' => '2020-01-02', 'count' => 4),
            array('date' => '2020-01-03', 'count' => 5)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 2),
            array('date' => '2020-01-03', 'count' => 4),
            array('date' => '2020-01-05', 'count' => 6)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5));
    }

    public function testHasSuiteDayWithEqualOrMore()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $method = new \ReflectionMethod(BadgeService::class, 'hasSuiteDayWithEqualOrMore');
        $method->setAccessible(true);

        $container = $kernel->getContainer();
        $service = $container->get(BadgeService::class);

        /* ************ */
        /* RETURN FALSE */
        /* ************ */

        $arrayCountByDay = null;
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 1)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 1),
            array('date' => '2020-01-02', 'count' => 1)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 6)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 1),
            array('date' => '2020-01-02', 'count' => 2),
            array('date' => '2020-01-03', 'count' => 4)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-03', 'count' => 5),
            array('date' => '2020-01-04', 'count' => 5)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 5),
            array('date' => '2020-01-04', 'count' => 5)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 6),
            array('date' => '2020-01-03', 'count' => 4),
            array('date' => '2020-01-04', 'count' => 5)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 4),
            array('date' => '2020-01-03', 'count' => 6),
            array('date' => '2020-01-04', 'count' => 5)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 5),
            array('date' => '2020-01-04', 'count' => 5),
            array('date' => '2020-01-05', 'count' => 5)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-03', 'count' => 5),
            array('date' => '2020-01-04', 'count' => 5),
            array('date' => '2020-01-06', 'count' => 5)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-03', 'count' => 5),
            array('date' => '2020-01-04', 'count' => 5),
            array('date' => '2020-01-05', 'count' => 4)
        );
        $this->assertFalse($method->invoke($service, $arrayCountByDay, 5, 3));

        /* *********** */
        /* RETURN TRUE */
        /* *********** */

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 5),
            array('date' => '2020-01-03', 'count' => 5)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 6),
            array('date' => '2020-01-02', 'count' => 5),
            array('date' => '2020-01-03', 'count' => 5)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 6),
            array('date' => '2020-01-03', 'count' => 6)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 4),
            array('date' => '2020-01-02', 'count' => 5),
            array('date' => '2020-01-03', 'count' => 5),
            array('date' => '2020-01-04', 'count' => 5)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 5),
            array('date' => '2020-01-02', 'count' => 5),
            array('date' => '2020-01-03', 'count' => 5),
            array('date' => '2020-01-04', 'count' => 4)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 4),
            array('date' => '2020-01-03', 'count' => 5),
            array('date' => '2020-01-04', 'count' => 5),
            array('date' => '2020-01-05', 'count' => 6)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5, 3));

        $arrayCountByDay = array(
            array('date' => '2020-01-01', 'count' => 1),
            array('date' => '2020-01-02', 'count' => 4),
            array('date' => '2020-01-03', 'count' => 3),
            array('date' => '2020-01-04', 'count' => 6),
            array('date' => '2020-01-05', 'count' => 5),
            array('date' => '2020-01-06', 'count' => 6)
        );
        $this->assertTrue($method->invoke($service, $arrayCountByDay, 5, 3));
    }
}
