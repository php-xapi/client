Xabbuh xApi Client
==================

[![Build Status](https://travis-ci.org/xabbuh/xapi-client.png)](https://travis-ci.org/xabbuh/xapi-client)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xabbuh/xapi-client/badges/quality-score.png?s=769c1e047e4dbd4d5cdce1008098f9965dfb7924)](https://scrutinizer-ci.com/g/xabbuh/xapi-client/)

Client side PHP implementation of the
[Experience API](https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md).

Installation
------------

The recommended way to install the xAPI client is using
[Composer](http://getcomposer.org/):

1. Download and install Composer:

        curl -sS http://getcomposer.org/installer | php

   For more details on how to install composer have a look at the official
   [documentation](http://getcomposer.org/doc/00-intro.md).

1. Add ``xabbuh/xapi-client`` and ``xabbuh/xapi-common`` as dependencies in your
   project's ``composer.json`` file:

    ```yaml
    {
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/xabbuh/serializer.git"
            }
        ],
        "require": {
            "jms/serializer": "dev-polymorphic-bugfix",
            "xabbuh/xapi-client": "~1.0@dev",
            "xabbuh/xapi-common": "~1.0@dev"
        }
    }
    ```

1. Install your dependencies:

        php composer.phar install

1. Require Composer's autoloader:

   ``` php
   require 'vendor/autoload.php';
   ```

Usage
-----

Read the [documentation](doc/usage.md) to find out how to use the library.

License
-------

This package is under the MIT license. See the complete license in the
[LICENSE](LICENSE) file.
