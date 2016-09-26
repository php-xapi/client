UPGRADE
=======

Upgrading from 0.2 to 0.3
-------------------------

* All classes are final now which means that you can now longer extend them.
  Consider using composition/decoration instead if you need to build functionality
  on top of the built-in classes.

Upgrading from 0.1 to 0.2
-------------------------

* Statement identifiers must be passed as `StatementId` objects instead of
  strings.
