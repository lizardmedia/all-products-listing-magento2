<?php

declare(strict_types=1);

/**
 * File: RootCategoryProvider.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\AllProductsListing\Model;

use LizardMedia\AllProductsListing\Api\RootCategoryProviderInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RootCategoryProvider
 * @package LizardMedia\AllProductsListing\Model
 */
class RootCategoryProvider implements RootCategoryProviderInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * RootCategoryProvider constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository, StoreManagerInterface $storeManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    public function getRootCategory(): CategoryInterface
    {
        $store = $this->storeManager->getStore();
        $rootCategoryId = $store->getRootCategoryId();
        $rootCategory = $this->categoryRepository->get($rootCategoryId, $store->getId());
        return $rootCategory;
    }
}
