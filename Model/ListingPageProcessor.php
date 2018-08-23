<?php

declare(strict_types=1);

/**
 * File: ListingPageProcessor.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\AllProductsListing\Model;

use LizardMedia\AllProductsListing\Api\ListingPageProcessorInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Catalog\Model\Design;
use Magento\Framework\DataObject;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Result\Page;

/**
 * Class ListingPageProcessor
 * @package LizardMedia\AllProductsListing\Model
 */
class ListingPageProcessor implements ListingPageProcessorInterface
{
    /**
     * @var Design
     */
    private $catalogDesign;

    /**
     * @var CategoryUrlPathGenerator
     */
    private $categoryUrlPathGenerator;

    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * ListingPageProcessor constructor.
     * @param Design $design
     * @param CategoryUrlPathGenerator $categoryUrlPathGenerator
     */
    public function __construct(Design $design, CategoryUrlPathGenerator $categoryUrlPathGenerator)
    {
        $this->catalogDesign = $design;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
    }

    /**
     * @param CategoryInterface | Category $category
     * @param Page $page
     * @return void
     */
    public function process(CategoryInterface $category, Page $page): void
    {
        $settings = $this->catalogDesign->getDesignSettings($category);
        $this->pageConfig = $page->getConfig();
        $this->applyCustomDesign($settings);

        $this->applyCustomPageLayout($page, $settings);
        $type = $this->getCategoryType($category);
        $this->addPageLayoutHandles($category, $page, $type);
        $this->applyLayoutUpdates($page, $settings);
        $this->configurePage($page, $category);
    }

    /**
     * @param DataObject $settings
     * @return void
     */
    private function applyCustomDesign(DataObject $settings) : void
    {
        $customDesign = $settings->getCustomDesign();
        if ($customDesign) {
            $this->catalogDesign->applyCustomDesign($customDesign);
        }
    }

    /**
     * @param Page $page
     * @param DataObject $settings
     */
    private function applyCustomPageLayout(Page $page, DataObject $settings) : void
    {
        $pageLayout = $settings->getPageLayout();
        if ($pageLayout) {
            $this->pageConfig->setPageLayout($pageLayout);
        }
    }

    /**
     * @param CategoryInterface | Category $category
     * @return string
     */
    private function getCategoryType(CategoryInterface $category) : string
    {
        if ($category->getData('is_anchor')) {
            $type = $category->hasChildren() ? 'layered' : 'layered_without_children';
        } else {
            $type = $category->hasChildren() ? 'default' : 'default_without_children';
        }

        return $type;
    }

    /**
     * @param CategoryInterface | Category  $category
     * @param Page $page
     * @param string $type
     * @return void
     */
    private function addPageLayoutHandles(CategoryInterface $category, Page $page, string $type) : void
    {
        if (!$category->hasChildren()) {
            $parentType = strtok($type, '_');
            $page->addPageLayoutHandles(['type' => $parentType]);
        }

        $page->addPageLayoutHandles(['type' => $type, 'id' => $category->getId()]);
    }

    /**
     * @param Page $page
     * @param DataObject $settings
     * @return void
     */
    private function applyLayoutUpdates(Page $page, DataObject $settings) : void
    {
        $layoutUpdates = $settings->getLayoutUpdates();
        if ($layoutUpdates && is_array($layoutUpdates)) {
            foreach ($layoutUpdates as $layoutUpdate) {
                $page->addUpdate($layoutUpdate);
            }
        }
    }

    /**
     * @param Page $page
     * @param CategoryInterface $category
     * @return void
     */
    private function configurePage(Page $page, CategoryInterface $category) : void
    {
        $this->pageConfig->addBodyClass('page-products')
            ->addBodyClass(sprintf('categorypath-%s', $this->categoryUrlPathGenerator->getUrlPath($category)))
            ->addBodyClass(sprintf('category-%s', $category->getUrlKey()));
        $this->pageConfig->getTitle()->set(__('All products'));
    }
}
