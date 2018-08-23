<?php

declare(strict_types=1);

/**
 * File: RootCategoryProviderTest.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\AllProductsListing\Test\Unit\Model;

use LizardMedia\AllProductsListing\Model\RootCategoryProvider;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class RootCategoryProviderTest
 * @package LizardMedia\AllProductsListing\Test\Unit\Model
 */
class RootCategoryProviderTest extends TestCase
{
    /**
     * @var RootCategoryProvider
     */
    private $rootCategoryProvider;

    /**
     * @var MockObject | CategoryInterface
     */
    private $categoryMock;

    /**
     * @var MockObject | CategoryRepositoryInterface
     */
    private $categoryRepositoryMock;

    /**
     * @var MockObject | Store | StoreInterface
     */
    private $storeMock;

    /**
     * @var MockObject | StoreManagerInterface
     */
    private $storeManagerMock;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        //Internal mocks
        $this->categoryMock = $this->getMockBuilder(CategoryInterface::class)->getMock();
        $this->storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();


        //Dependencies mocks
        $this->categoryRepositoryMock = $this->getMockBuilder(CategoryRepositoryInterface::class)
            ->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)->getMock();


        $this->rootCategoryProvider = new RootCategoryProvider(
            $this->categoryRepositoryMock,
            $this->storeManagerMock
        );
    }

    /**
     * @test
     * @throws NoSuchEntityException
     */
    public function testProvideWhenStoreRepositoryThrowsException()
    {
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $this->categoryRepositoryMock->expects($this->never())->method('get');

        $this->expectException(NoSuchEntityException::class);
        $this->rootCategoryProvider->getRootCategory();
    }

    /**
     * @test
     * @throws NoSuchEntityException
     */
    public function testProvideWhenCategoryRepositoryThrowsExceptionAndCategoryNotFound()
    {
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getRootCategoryId')
            ->willReturn(1);

        $this->storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with(1, 2)
            ->willThrowException(new NoSuchEntityException());

        $this->expectException(NoSuchEntityException::class);
        $this->rootCategoryProvider->getRootCategory();
    }

    /**
     * @test
     * @throws NoSuchEntityException
     */
    public function testProvideWhenNoExceptionsAndCategoryFound()
    {
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getRootCategoryId')
            ->willReturn(1);

        $this->storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with(1, 2)
            ->willReturn($this->categoryMock);

        $this->assertInstanceOf(CategoryInterface::class, $this->rootCategoryProvider->getRootCategory());
    }
}
