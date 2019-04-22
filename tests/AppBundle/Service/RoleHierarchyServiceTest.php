<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\RoleHierarchyService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Role\Role;

class RoleHierarchyServiceTest extends KernelTestCase
{
    public function testBuildRoleMap()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $method = new \ReflectionMethod(RoleHierarchyService::class, 'buildRoleMap');
        $method->setAccessible(true);

        $container = $kernel->getContainer();
        $service = $container->get(RoleHierarchyService::class);
        
        $res = $method->invoke($service);

        $attendu = array(
            'ROLE_SUPER_ADMIN' => array('ROLE_ADMINISTRATEUR'),
            'ROLE_ADMINISTRATEUR' => array('ROLE_MODERATEUR'),
            'ROLE_MODERATEUR' => array('ROLE_USER')
        );
        
        $this->assertEquals($attendu, $res);
    }

    public function testGetReachableRolesAdministrateur()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $service = $container->get(RoleHierarchyService::class);
        
        $roles = array(
            new Role('ROLE_ADMINISTRATEUR')
        );
        
        $res = $service->getReachableRoles($roles);

        $attendu = array(
            new Role('ROLE_MODERATEUR'),
            new Role('ROLE_USER')
        );

        $this->assertEquals($attendu, $res);
    }

    public function testGetReachableRolesModerateur()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $service = $container->get(RoleHierarchyService::class);

        $roles = array(
            new Role('ROLE_MODERATEUR')
        );

        $res = $service->getReachableRoles($roles);

        $attendu = array(
            new Role('ROLE_USER')
        );

        $this->assertEquals($attendu, $res);
    }

    public function testGetReachableRolesUser()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $service = $container->get(RoleHierarchyService::class);

        $roles = array(
            new Role('ROLE_USER')
        );

        $res = $service->getReachableRoles($roles);

        $attendu = array();
        
        $this->assertEquals($attendu, $res);
    }
}