<?php

declare(strict_types=1);

/**
 * File: ModuleConfigTest.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\AllProductsListing\Test\Integration;

use \Magento\Framework\Component\ComponentRegistrar;
use \Magento\Framework\Module\ModuleList;
use \Magento\TestFramework\Helper\Bootstrap;
use \PHPUnit\Framework\TestCase;

/**
 * Class ModuleConfigTest
 * @package LizardMedia\AllProductsListing\Test\Integration
 */
class ModuleConfigTest extends TestCase
{
    /**
     * @const string
     */
    const MODULE_NAME = 'LizardMedia_AllProductsListing';


    /**
     * @test
     */
    public function testTheModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $this->assertArrayHasKey(self::MODULE_NAME, $registrar->getPaths(ComponentRegistrar::MODULE));
    }


    /**
     * @test
     */
    public function testTheModuleIsConfiguredAndEnabled()
    {
        $objectManager = Bootstrap::getObjectManager();
        $moduleList = $objectManager->create(ModuleList::class);
        $this->assertTrue($moduleList->has(self::MODULE_NAME));
    }
}
