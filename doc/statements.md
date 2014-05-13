The Statements API
==================

Storing Statements
------------------

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

Retrieving Statements
---------------------

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
$filter
    ->byActor($actor)                // filter by Actor
    ->byVerb($verb)                  // filter by Verb
    ->byActivity($activity)          // filter by Activity
    ->byRegistration(...)            // filter for Statements matching the given
                                     // registration id
    ->enableRelatedActivityFilter()  // apply the Activity filter to Sub-Statements
    ->disableRelatedActivityFilter() // apply the Activity filter to Sub-Statements
    ->enableRelatedAgentFilter()     // apply the Agent filter to Sub-Statements
    ->disableRelatedAgentFilter()    // apply the Agent filter to Sub-Statements
    ->since(new \DateTime(...))      // filter for Statements stored since
                                     // the given timestamp
    ->until(new \DateTime(...))      // filter for Statements stored before
                                     // the given timestamp
    ->limit(5)                       // limit the number of Statements returned
    ->format(...)                    // the result format (one of "ids", "exact",
                                     // "canonical")
    ->includeAttachments()           // return Statements with attachments included
    ->excludeAttachments()           // return Statements without attachments
    ->ascending()                    // ascending order of stored time
    ->descending();                  // ascending order of stored time

$result = $xApiClient->getStatements($filter->getFilter());
```

If you limited the number of returned results, you can get the next Statements
by calling the ``getNextStatements()`` method passing the ``StatementResult``
of the previous request to it:

```php
// ....
$filter = new StatementsFilter();
$filter->limit(3);
$firstStatementResult = $xApiClient->getStatements($filter);

// get the next Statements
$nextStatementResult = $xApiClient->getNextStatements($firstStatementResult);
```

The Experience API doesn't allow to delete Statements. You have to mark them as
voided instead:

```php
// ...
$statement = ...; // The Statement being voided
$actor = ...; // The Actor voiding the Statement
$xApiClient->voidStatement($statement, $actor);
```

Voided Statements won't be returned when requesting either a single Statement or
a collection of Statements. Though, you can retrieve a single voided Statement
using the ``getVoidedStatement()`` method:

```php
// ...
$voidedStatement = $xApiClient->getVoidedStatement($statementId);
```
