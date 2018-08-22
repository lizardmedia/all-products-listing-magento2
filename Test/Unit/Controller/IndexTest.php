<?php

declare(strict_types=1);

/**
 * File: IndexTest.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\AllProductsListing\Test\Unit\Controller;

use LizardMedia\AllProductsListing\Api\ListingPageProcessorInterface;
use LizardMedia\AllProductsListing\Api\RootCategoryProviderInterface;
use LizardMedia\AllProductsListing\Controller\Index\Index;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexTest
 * @package LizardMedia\AllProductsListing\Test\Unit\Controller
 */
class IndexTest extends TestCase
{
    /**
     * @var MockObject | ListingPageProcessorInterface
     */
    private $listingPageProcessorMock;

    /**
     * @var MockObject | RootCategoryProviderInterface
     */
    private $rootCategoryProviderMock;

    /**
     * @var Index
     */
    private $index;

    /**
     * @var MockObject | CategoryInterface
     */
    private $categoryMock;

    /**
     * @var MockObject | Resolver
     */
    private $layerResolverMock;

    /**
     * @var MockObject | Session
     */
    private $catalogSessionMock;

    /**
     * @var MockObject | Forward
     */
    private $resultForwardMock;

    /**
     * @var MockObject | ForwardFactory
     */
    private $resultForwardFactoryMock;

    /**
     * @var MockObject | Page
     */
    private $resultPageMock;

    /**
     * @var MockObject | PageFactory
     */
    private $resultPageFactoryMock;

    /**
     * @var MockObject | EventManager
     */
    private $eventManager;

    /**
     * @var MockObject | Registry
     */
    private $registryMock;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        //Internal mocks
        $this->categoryMock = $this->getMockBuilder(CategoryInterface::class)->getMock();
        $this->resultForwardMock = $this->getMockBuilder(Forward::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventManager = $this->getMockBuilder(EventManager::class)
            ->disableOriginalConstructor()
            ->getMock();


        //Dependencies mocks
        $this->listingPageProcessorMock = $this->getMockBuilder(ListingPageProcessorInterface::class)->getMock();
        $this->rootCategoryProviderMock = $this->getMockBuilder(RootCategoryProviderInterface::class)->getMock();
        $this->layerResolverMock = $this->getMockBuilder(Resolver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->catalogSessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultForwardFactoryMock = $this->getMockBuilder(ForwardFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->getMockBuilder(Registry::class)->getMock();

        $context->expects($this->once())
            ->method('getEventManager')
            ->willReturn($this->eventManager);

        $this->index = new Index(
            $this->listingPageProcessorMock,
            $this->rootCategoryProviderMock,
            $this->layerResolverMock,
            $this->catalogSessionMock,
            $context,
            $this->resultForwardFactoryMock,
            $this->registryMock,
            $this->resultPageFactoryMock
        );
    }

    /**
     * @test
     */
    public function testExecuteWhenExceptionIsThrownDuringCategoryInitialization()
    {
        $this->rootCategoryProviderMock->expects($this->once())
            ->method('getRootCategory')
            ->willThrowException(new NoSuchEntityException());

        $this->catalogSessionMock->expects($this->never())->method('__call');

        $this->resultForwardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultForwardMock);

        $this->resultForwardMock->expects($this->once())
            ->method('forward')
            ->with('noroute')
            ->willReturn($this->resultForwardMock);

        $this->assertInstanceOf(Forward::class, $this->index->execute());
    }

    /**
     * @test
     */
    public function testExecuteWhenLayerResolverThrowsException()
    {
        $this->expectationsForInitializationOfRootCategory();

        $this->layerResolverMock->expects($this->once())
            ->method('create')
            ->with(Resolver::CATALOG_LAYER_CATEGORY)
            ->willThrowException(new \Exception());

        $this->resultForwardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultForwardMock);

        $this->resultForwardMock->expects($this->once())
            ->method('forward')
            ->with('noroute')
            ->willReturn($this->resultForwardMock);

        $this->assertInstanceOf(Forward::class, $this->index->execute());
    }

    /**
     * @test
     */
    public function testExecuteWhenPageCorrectlyBuilt()
    {
        $this->expectationsForInitializationOfRootCategory();

        $this->layerResolverMock->expects($this->once())
            ->method('create')
            ->with(Resolver::CATALOG_LAYER_CATEGORY);

        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultPageMock);

        $this->listingPageProcessorMock->expects($this->once())
            ->method('process')
            ->with($this->categoryMock, $this->resultPageMock);

        $this->assertInstanceOf(
            Page::class,
            $this->index->execute()
        );
    }

    /**
     * @return void
     */
    private function expectationsForInitializationOfRootCategory() : void
    {
        $this->rootCategoryProviderMock->expects($this->once())
            ->method('getRootCategory')
            ->willReturn($this->categoryMock);

        $this->registryMock->expects($this->once())
            ->method('register')
            ->with('current_category', $this->categoryMock);

        $this->eventManager->expects($this->once())
            ->method('dispatch')
            ->with(
                'catalog_controller_category_init_after',
                ['category' => $this->categoryMock, 'controller_action' => $this->index]
            );
    }
}
