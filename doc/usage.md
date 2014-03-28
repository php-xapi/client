Usage
=====

Building the Client
-------------------

The xAPI client library ships with a builder class which eases the process of
creating an instance of an ``XApiClient`` class:

```php
use Xabbuh\XApi\Client\XApiClientBuilder;

$builder = new XApiClientBuilder();
$xApiClient = $builder->setBaseUrl('http://example.com/lrs/api')
    ->setVersion('1.0.0')
    ->build();
```

The builder creates a client for the 1.0.1 API version if you don't set a version.

### HTTP Basic Authentication

Use the ``setAuth()`` method if access to the LRS resources is protected with
HTTP Basic authentication:

```php
use Xabbuh\XApi\Client\XApiClientBuilder;

$builder = new XApiClientBuilder();
$xApiClient = $builder->setBaseUrl('http://example.com/lrs/api')
    ->setAuth('username', 'password')
    ->build();
```

### OAuth1 Authentication

Using the ``setOAuthCredentials()`` method, you can configure the client to
access OAuth1 protected resources:

```php
use Xabbuh\XApi\Client\XApiClientBuilder;

$builder = new XApiClientBuilder();
$xApiClient = $builder->setBaseUrl('http://example.com/lrs/api')
    ->setOAuthCredentials('consumer-key', 'consumer-secret', 'token', 'token-secret')
    ->build();
```

Requesting the API
------------------

The ``XApiClient`` class provides methods to easily access each xAPI endpoint
of a learning record store.

### Statements

#### Storing Statements

The ``storeStatement()`` and ``storeStatements()`` methods can be used to store
a single Statement or a collection of Statements. Both method return the stored
Statement(s) each having a unique id created by the remote LRS.

```php

use Xabbuh\XApi\Common\Model\Statement;

$xApiClient = ...;

$statement = new Statement();
// ...

// store a single Statement
$xApiClient->storeStatement($statement);

$statement2 = new Statement();
// ...

// store a collection of clients
$xApiClient->storeStatements(array($statement, $statement2));
```

### Retrieving Statements

Use the ``getStatement()`` method to obtain a certain Statement given its id:

```php
// ...

// get a single Statement
$statement = $xApiClient->getStatement($statementId);
```

``getStatements()`` returns a collection of Statements encapsulated in a
StatementResult instance:

```php
// ...

// returns all accessible Statements
$result = $xApiClient->getStatements();
```

You can even filter Statements using a StatementFilter:

```php
use Xabbuh\XApi\Client\StatementsFilter;

// ...
$filter = new StatementsFilter();
$filter->byActor($actor)        // filter by Actor
    ->byVerb($verb)             // filter by Verb
    ->byActivity($activity)     // filter by Activity
    ->since(new \DateTime(...)) // filter for Statements stored since
                                // the given timestamp
    ->until(new \DateTime(...)) // filter for Statements stored before
                                // the given timestamp
    ->limit(5)                  // limit the number of Statements returned
    ->ascending()               // ascending order of stored time
    ->descending();              // ascending order of stored time

$result = $xApiClient->getStatements($filter->getFilter());
```