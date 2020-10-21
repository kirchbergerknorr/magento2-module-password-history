# Snk Customer Password History

**Extension for Magento 2**

--- 

## Overview

PCI DSS requires that customers cannot use their old passwords when they need to change them. 

Magento 2 does not have a password history for frontend users and this module adds the feature.

## Requirements and setup

Tested for Magento 2.3.5 and PHP 7.2

### Installation

Can be installed with composer:

```composer require snk/magento2-module-password-history```


## Details

### Configuration

The module adds following config fields under _Stores->Configuration->Customer->Password Options_:
 
 - _Enable Password History Restriction_: enable of disable the feature for website
 - _Password History Size_: max number of old passwords to keep in the database for per user.
 - _Password History Message_: Message the user sees when they try to use a password that has already been used (present in the DB)
 
## Authors

Oleh Kravets [oleh.kravets@snk.de](mailto:oleh.kravets@snk.de)

## License

[MIT License](https://opensource.org/licenses/MIT)
