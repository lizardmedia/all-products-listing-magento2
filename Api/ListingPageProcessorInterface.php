<?php

declare(strict_types=1);

/**
 * File: ListingPageProcessorInterface.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\AllProductsListing\Api;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\View\Result\Page;

/**
 * Interface ListingPageProcessorInterface
 * @package LizardMedia\AllProductsListing\Api
 */
interface ListingPageProcessorInterface
{
    /**
     * @param CategoryInterface $category
     * @param Page $page
     * @return void
     */
    public function process(CategoryInterface $category, Page $page) : void;
}
