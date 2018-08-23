<?php

declare(strict_types=1);

/**
 * File: Index.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\AllProductsListing\Controller\Index;

use LizardMedia\AllProductsListing\Api\ListingPageProcessorInterface;
use LizardMedia\AllProductsListing\Api\RootCategoryProviderInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package LizardMedia\AllProductsListing\Controller\Index
 */
class Index extends Action
{
    /**
     * @var ListingPageProcessorInterface
     */
    private $listingPageProcessor;

    /**
    * @var RootCategoryProviderInterface
     */
    private $rootCategoryProvider;

    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var Session
     */
    private $catalogSession;

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Index constructor.
     * @param ListingPageProcessorInterface $listingPageProcessor
     * @param RootCategoryProviderInterface $rootCategoryProvider
     * @param Resolver $layerResolver
     * @param Session $catalogSession
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        ListingPageProcessorInterface $listingPageProcessor,
        RootCategoryProviderInterface $rootCategoryProvider,
        Resolver $layerResolver,
        Session $catalogSession,
        Context $context,
        ForwardFactory $resultForwardFactory,
        Registry $registry,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->listingPageProcessor = $listingPageProcessor;
        $this->rootCategoryProvider = $rootCategoryProvider;
        $this->layerResolver = $layerResolver;
        $this->catalogSession = $catalogSession;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
    }

    /**
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    private function initRootCategory() : CategoryInterface
    {
        $category = $this->rootCategoryProvider->getRootCategory();
        $this->catalogSession->setLastVisitedCategoryId($category->getId());
        $this->registry->register('current_category', $category);
        $this->_eventManager->dispatch(
            'catalog_controller_category_init_after',
            ['category' => $category, 'controller_action' => $this]
        );

        return $category;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        try {
            $category = $this->initRootCategory();
            $this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
        } catch (\Exception $exception) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $this->catalogSession->setLastViewedCategoryId($category->getId());
        $page = $this->resultPageFactory->create();
        $this->listingPageProcessor->process($category, $page);

        return $page;
    }
}
