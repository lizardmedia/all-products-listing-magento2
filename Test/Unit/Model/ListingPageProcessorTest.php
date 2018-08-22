<?php

declare(strict_types=1);

/**
 * File: ListingPageProcessorTest.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\AllProductsListing\Test\Unit\Model;

use LizardMedia\AllProductsListing\Model\ListingPageProcessor;
use LizardMedia\AllProductsListing\Test\Unit\Model\ListingPageProcessorTest\Settings;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Design;
use Magento\Framework\DataObject;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Page\Title;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ListingPageProcessorTest
 * @package LizardMedia\AllProductsListing\Test\Unit\Model
 */
class ListingPageProcessorTest extends TestCase
{
    /**
     * @var ListingPageProcessor
     */
    private $listingPageProcessor;

    /**
     * @var MockObject | Category | CategoryInterface
     */
    private $categoryMock;

    /**
     * @var MockObject | Design
     */
    private $catalogDesignMock;

    /**
     * @var MockObject | CategoryUrlPathGenerator
     */
    private $categoryUrlPathGeneratorMock;

    /**
     * @var MockObject | Settings | DataObject
     */
    private $settingsMock;

    /**
     * @var MockObject | Config
     */
    private $pageConfig;

    /**
     * @var MockObject | Page
     */
    private $resultPageMock;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        //Internal mocks
        $this->categoryMock = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->settingsMock = $this->getMockBuilder(Settings::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();

        //Dependencies mocks
        $this->catalogDesignMock = $this->getMockBuilder(Design::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryUrlPathGeneratorMock = $this->getMockBuilder(CategoryUrlPathGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->listingPageProcessor = new ListingPageProcessor(
            $this->catalogDesignMock,
            $this->categoryUrlPathGeneratorMock
        );
    }

    /**
     * @test
     */
    public function testProcessWithNoCustomizations()
    {
        $this->catalogDesignMock->expects($this->once())
            ->method('getDesignSettings')
            ->with($this->categoryMock)
            ->willReturn($this->settingsMock);

        $this->resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->pageConfig);

        $this->settingsMock->expects($this->once())
            ->method('getCustomDesign')
            ->willReturn(null);

        $this->settingsMock->expects($this->once())
            ->method('getPageLayout')
            ->willReturn(null);

        $this->categoryMock->expects($this->once())
            ->method('getData')
            ->with('is_anchor')
            ->willReturn(false);

        $this->categoryMock->expects($this->exactly(2))
            ->method('hasChildren')
            ->willReturn(true);

        $this->resultPageMock->expects($this->once())
            ->method('addPageLayoutHandles');

        $this->settingsMock->expects($this->once())
            ->method('getLayoutUpdates')
            ->willReturn(null);

        $this->expectationsForPageConfiguring();

        $this->listingPageProcessor->process($this->categoryMock, $this->resultPageMock);
    }

    /**
     * @test
     */
    public function testProcessWithCustomizations()
    {
        $this->catalogDesignMock->expects($this->once())
            ->method('getDesignSettings')
            ->with($this->categoryMock)
            ->willReturn($this->settingsMock);

        $this->resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->pageConfig);

        $this->settingsMock->expects($this->once())
            ->method('getCustomDesign')
            ->willReturn('some design');

        $this->catalogDesignMock->expects($this->once())
            ->method('applyCustomDesign')
            ->with('some design');

        $this->settingsMock->expects($this->once())
            ->method('getPageLayout')
            ->willReturn('handle');

        $this->pageConfig->expects($this->once())
            ->method('setPageLayout')
            ->with('handle');

        $this->categoryMock->expects($this->once())
            ->method('getData')
            ->with('is_anchor')
            ->willReturn(true);

        $this->categoryMock->expects($this->exactly(2))
            ->method('hasChildren')
            ->willReturn(true);

        $this->resultPageMock->expects($this->once())
            ->method('addPageLayoutHandles');

        $this->settingsMock->expects($this->once())
            ->method('getLayoutUpdates')
            ->willReturn(['update']);

        $this->expectationsForPageConfiguring();

        $this->listingPageProcessor->process($this->categoryMock, $this->resultPageMock);
    }

    /**
     * @return void
     */
    private function expectationsForPageConfiguring() : void
    {
        $this->pageConfig->expects($this->exactly(3))
            ->method('addBodyClass')
            ->willReturn($this->pageConfig);

        $title = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pageConfig->expects($this->once())
            ->method('getTitle')
            ->willReturn($title);

        $title->expects($this->once())
            ->method('set');
    }
}
