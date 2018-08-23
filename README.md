[![Latest Stable Version](https://poser.pugx.org/lizardmedia/module-all-products-listing/v/stable)](https://packagist.org/packages/lizardmedia/module-all-products-listing)
[![Total Downloads](https://poser.pugx.org/lizardmedia/module-all-products-listing/downloads)](https://packagist.org/packages/lizardmedia/module-all-products-listing)
[![License](https://poser.pugx.org/lizardmedia/module-all-products-listing/license)](https://packagist.org/packages/lizardmedia/module-all-products-listing)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lizardmedia/all-products-listing-magento2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lizardmedia/all-products-listing-magento2/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/lizardmedia/all-products-listing-magento2/badges/build.png?b=master)](https://scrutinizer-ci.com/g/lizardmedia/all-products-listing-magento2/build-status/master)

# Lizard Media All Products Listing

## Overview
Module provides listing displaying all products, within limits of root category

### Features
* by default listing is located under ../catalog_all/index/index


## Prerequisites
Magento 2.2 or higher
PHP 7.1


## Installing ##

You can install the module by downloading a .zip file and unpacking it inside
``app/code/LizardMedia/AllProductsListing`` directory inside your Magento
or via Composer (required).

To install the module via Composer simply run
```
composer require lizardmedia/module-all-products-listing
```

Than enable the module by running these command in the root of your Magento installation
```
bin/magento module:enable LizardMedia_AllProductsListing
bin/magento setup:upgrade
```


## Usage ##

#### Changing listing url ####
In order to change url of listing to more user-friendly use native Magento feature - url rewrite.
As target path should be used controller url - ```catalog_all/index/index```.


## Versioning ##

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags).

## Authors

* **Bartosz Kubicki** - [Lizard Media](https://github.com/lizardmedia)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details