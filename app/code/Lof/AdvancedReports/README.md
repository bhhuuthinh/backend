# Mage2 Module Lof Advanced Reports
Magento 2 Advanced Reports offer sales performance status with 28+ premade reports. Get new insights over how customers utilize and get predict sales trends. Using Magento 2 custom reports extension, you will get a variety of sale reports with priceless statistical data.

    ``landofcoder/module-reorder-product``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)

## Main Functionalities


## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lof`
 - Enable the module by running `php bin/magento module:enable Lof_AdvancedReports`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/module-reorder-product`
 - enable the module by running `php bin/magento module:enable Lof_AdvancedReports`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`
