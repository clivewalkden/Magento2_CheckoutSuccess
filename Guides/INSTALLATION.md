# Magento 2 Checkout Success Plugin Installation

**Using Composer:**
1. Install using composer `composer require clivewalkden/magento2-checkoutsuccess`
1. Enable the module `php -f bin/magento module:enable --clear-static-content CliveWalkden_CheckoutSuccess`
1. Database updates `php -f bin/magento setup:upgrade` 
1. Configure the module in the Magento 2 Admin. Go to Stores -> Configuration -> Advanced -> Developer -> Checkout Success Testing Module
