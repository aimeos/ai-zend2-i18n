<a href="https://aimeos.org/">
    <img src="https://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

# Aimeos Zend2 I18n adapter

[![Build Status](https://travis-ci.org/aimeos/ai-zend2-i18n.svg)](https://travis-ci.org/aimeos/ai-zend2-i18n)
[![Coverage Status](https://coveralls.io/repos/aimeos/ai-zend2-i18n/badge.svg?branch=master&service=github)](https://coveralls.io/github/aimeos/ai-zend2-i18n?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aimeos/ai-zend2-i18n/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aimeos/ai-zend2-i18n/?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/aimeos/ai-zend2-i18n.svg)](http://hhvm.h4cc.de/package/aimeos/ai-zend2-i18n)

The Aimeos web shop components can integrate into almost any PHP application and uses the infrastructure of the application for building URLs, caching content, configuration settings, logging messages, session handling, sending e-mails or handling translations.

The ai-zend2-i18n extension integrates the Zend I18n component for translating messages into Aimeos. It's useful if the Aimeos translations should be available in your application.

## Table of content

- [Installation](#installation)
- [Setup](#setup)
- [License](#license)
- [Links](#links)

## Installation

To allow the Aimeos web shop components to retrive translations for the used strings, you have to install the adapter first. As every Aimeos extension, the easiest way is to install it via [composer](https://getcomposer.org/). If you don't have composer installed yet, you can execute this string on the command line to download it:
```
php -r "readfile('https://getcomposer.org/installer');" | php -- --filename=composer
```

Add the ai-zend2-i18n extension to the "require" section of your ```composer.json``` file:
```
"require": [
    "aimeos/ai-zend2-i18n": "dev-master",
    ...
],
```
If you don't want to use the latest version, you can also install any release. The list of releases is available at [Packagist](https://packagist.org/packages/aimeos/ai-zend2-i18n). Afterwards you only need to execute the composer update command on the command line:
```
composer update
```

## Setup

Now add the Zend I18n object to the Aimeos context, which you have to create to get the Aimeos components running:
```
$i18nPaths = $aimeos->getI18nPaths();
$i18n = new \MW_Translation_Zend2( $i18nPaths, 'gettext', 'en', array( 'disableNotices' => true ) );
$context->setI18n( array( 'en' => $i18n );
```
The ```$aimeos``` object is an instance of the ```Arcavias``` class from the core. The code above would only set up the translation for English but you can also set up several languages at once and pass them in the array to the ```setI18n()``` method.

To speed up retrieving translated strings, you can wrap the translation object into the APC decorator before adding it to the context:
```
if( function_exists( 'apc_store' ) === true ) {
    $i18n = new \MW_Translation_Decorator_APC( $i18n, 'myApcPrefix:' ) );
}
```
This would use the shared memory of the web server to store and retrieve the strings from there instead of the binary gettext "mo" files.

To overwrite translations by local ones you can furthermore added them on top:
```
    $i18n = new \MW_Translation_Decorator_Memory( $i18n, array( /*...*/ ) );
```
All translations from the second parameter would be used instead of the ones from the gettext files. The format of the translations must be:
```
'<translation domain>' => array(
    '<original singular>' => array('<singular translation>','<plural translation>'),
),
'client/html' => array(
    'address' => array('Address','Addresses'),
),
```

## License

The Aimeos ai-zend2-i18n extension is licensed under the terms of the LGPLv3 license and is available for free.

## Links

* [Web site](https://aimeos.org/)
* [Documentation](https://aimeos.org/docs)
* [Help](https://aimeos.org/help)
* [Issue tracker](https://github.com/aimeos/ai-zend2-i18n/issues)
* [Source code](https://github.com/aimeos/ai-zend2-i18n)
