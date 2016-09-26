CHANGELOG
=========

0.3.0
-----

* Removed the `getRequestHandler()` method from the API classes:

  * `ActivityProfileApiClient::getRequestHandler()`
  * `AgentProfileApiClient::getRequestHandler()`
  * `ApiClient::getRequestHandler()`
  * `DocumentApiClient::getRequestHandler()`
  * `StateApiClient::getRequestHandler()`
  * `StatementsApiClient::getRequestHandler()`

* Removed the `getVersion()` method from the API interfaces:

  * `ActivityProfileApiClientInterface::getVersion()`
  * `AgentProfileApiClientInterface::getVersion()`
  * `StateApiClientInterface::getVersion()`
  * `StatementsApiClientInterface::getVersion()`

* Removed the `getVersion()` method from the API classes:

  * `ActivityProfileApiClient::getVersion()`
  * `AgentProfileApiClient::getVersion()`
  * `ApiClient::getVersion()`
  * `DocumentApiClient::getVersion()`
  * `StateApiClient::getVersion()`
  * `StatementsApiClient::getVersion()`
  * `XApiClient::getVersion()`

* Removed the `getUsername()` and `getPassword()` methods from the `HandlerInterface`
  and the `Handler` class.

* Removed the `getHttpClient()` method from the `Handler` class.

* Removed the `getSerializerRegistry()` method from the `XApiClient` class.

* Made all classes final.

0.2.0
-----

* made the client compatible with version 0.5 of the `php-xapi/model` package

* made the client compatible with version 0.3 of the `php-xapi/serializer` package

0.1.0
-----

First release of an Experience API client based on the Guzzle HTTP library.

This package replaces the `xabbuh/xapi-client` package which is now deprecated
and should no longer be used.
