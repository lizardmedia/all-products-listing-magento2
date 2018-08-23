<?php

declare(strict_types=1);

/**
 * File: RootCategoryProviderInterface.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\AllProductsListing\Api;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class RootCategoryProviderInterface
 * @package LizardMedia\AllProductsListing\Api
 */
interface RootCategoryProviderInterface
{
    /**
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    public function getRootCategory() : CategoryInterface;
}
