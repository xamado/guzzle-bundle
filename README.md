XamadoGuzzleBundle
===================

XamadoGuzzleBundle is a bundle for Symfony 2.1 that integrates the Guzzle library adding some useful features like object
serialization using the JMSSerializerBundle.

## Installation

### Step 1: Download XamadoGuzzleBundle

Since this is developed for Symfony 2.1 and onwards the install method is using Composer.

Add the following to your composer.json

``` json
"require": {
    ...
    "xamado/guzzle-bundle": "dev-master"
    ...
}
```

And now update your dependencies

``` bash
$ php composer.phar update
```

### Step 2: Enable the bundle

Enable the bundle in your AppKernel by adding it to the end of your bundles array

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Xamado\GuzzleBundle\XamadoGuzzleBundle()
    );
}
```

## Usage

Fill me in

## License

This bundle is under the MIT license.
