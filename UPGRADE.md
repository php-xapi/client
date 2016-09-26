UPGRADE
=======

Upgrading from 0.2 to 0.3
-------------------------

* Removed the `getRequestHandler()` method from the API classes:

  * `ActivityProfileApiClient::getRequestHandler()`
  * `AgentProfileApiClient::getRequestHandler()`
  * `ApiClient::getRequestHandler()`
  * `DocumentApiClient::getRequestHandler()`
  * `StateApiClient::getRequestHandler()`
  * `StatementsApiClient::getRequestHandler()`
  * `XApiClient::getRequestHandler()`

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

* All classes are final now which means that you can now longer extend them.
  Consider using composition/decoration instead if you need to build functionality
  on top of the built-in classes.

Upgrading from 0.1 to 0.2
-------------------------

* Statement identifiers must be passed as `StatementId` objects instead of
  strings.
