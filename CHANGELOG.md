CHANGELOG
=========

0.4.0
-----

* The `XApiClientBuilder` class now makes use of the `SerializerFactoryInterface`
  introduced in release `0.4.0` of the `php-xapi/serializer` package. By
  default, it will fall back to the `SerializerFactory` implemented provided
  by the `php-xapi/symfony-serializer` to maintain backwards-compatibility
  with the previous release. However, you are now able to inject arbitrary
  implementations of the `SerializerFactoryInterface` into the constructor
  of the `XApiClientBuilder` to use whatever alternative implementation
  (packages providing such an implementation should provide the virtual
  `php-xapi/serializer-implementation` package).

0.3.0
-----

* Do not send authentication headers when no credentials have been configured.

* Fixed treating HTTP methods case insensitive. Rejecting uppercased HTTP
  method names contradicts the HTTP specification. Lowercased method names
  will still be supported to keep backwards compatibility though.

* Fixed creating `XApiClient` instances in an invalid state. The `XApiClientBuilder`
  now throws a `\LogicException` when the `build()` method is called before
  a base URI was configured.

* Removed the `ApiClient` class. The `$requestHandler` and `$version` attributes
  have been moved to the former child classes of the `ApiClient` class and
  their visibility has been changed to `private`.

* The visibility of the `$documentDataSerializer` property of the `ActivityProfileApiClient`,
  `AgentProfileApiClient`, `DocumentApiClient`, and `StateApiClient` classes
  has been changed to `private`.

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
