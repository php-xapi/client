The Agent Profile API
=====================

Agent Profiles
--------------

A LMS can use the xAPI to store documents associated to a certain agent using
agent profiles. An agent profile is dedicated to an agent and a profile id:

```php
use Xabbuh\XApi\Common\Model\AgentProfile;

// ...
$profile = new AgentProfile();
$profile->setAgent($agent);
$profile->setProfileId($profileId);
```

Documents
---------

Documents are simple collections of key-value pairs and can be accessed like arrays:

```php
use Xabbuh\XApi\Common\Model\AgentProfileDocument;

// ...
$document = new AgentProfileDocument();
$document->setAgentProfile($profile);
$document['x'] = 'foo';
$document['y'] = 'bar';
```

Storing Agent Profile Documents
-------------------------------

You can simply store an ``AgentProfileDocument`` passing it to the
``createOrUpdateAgentProfileDocument()`` method of the xAPI client:

```php
$document = ...; // the agent profile document
$xApiClient->createOrUpdateAgentProfileDocument($document);
```

If a document already exists for this agent profile, the existing document will
be updated. This means that new fields will be updated, existing fields that are
included in the new document will be overwritten and existing fields that are
not included in the new document will be kept as they are.

If you want to replace a document, use the ``createOrReplaceAgentProfileDocument()``
method instead:

```php
$document = ...; // the agent profile document
$xApiClient->createOrReplaceAgentProfileDocument($document);
```

Deleting Agent Profile Documents
--------------------------------

An ``AgentProfileDocument`` is deleted by passing the particular ``AgentProfile``
to the ``deleteAgentProfileDocument()`` method:

```php
$profile = ...; // the agent profile the document should be deleted from
$xApiClient->deleteAgentProfileDocument($profile);
```

Retrieving Agent Profile Documents
----------------------------------

Similarly, you receive a document for a particular agent profile by passing the
profile to the ``getAgentProfileDocument()`` method:

```php
$profile = ...; // the agent profile the document should be retrieved from
$document = $xApiClient->getAgentProfileDocument($profile);
```
