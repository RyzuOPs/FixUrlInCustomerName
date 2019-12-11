# fixurlincustomername

Module for Prestashop 1.7 overriding isName() function in Validate.php, to prevent web robots from registering accounts with URLs in the name.

Module is inspired by this and here you can get more informations: https://github.com/PrestaShop/PrestaShop/pull/13549

## Download

You can download module here: [fixurlincustomername.zip](https://github.com/RyzuOPs/fixurlincustomername/releases/download/1.0/fixurlincustomername.zip)

## Use

Simply:
- install and enable module
- enable override in module settings
- check if you can register customer with URL in name, if not everything is OK   

This is fixed in Prestashop from 1.7.6. There is no sense to use this in versions greater than 1.7.5.2.
