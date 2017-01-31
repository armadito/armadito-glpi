Enrollment
==========

On either Linux or Windows, you need admin/superuser rights for enrollment.
It is due to the fact that armadito-agent retrieves your system UUID as an unique identifier.
Indeed, it allows automatic association with inventory in GLPI.

Be sure that you got a valid enrollment key, or you won't be able to enroll your agent.

To enroll/re-enroll an agent, just type the following with **admin/superuser** rights :

::

   $ armadito-agent -t "Enrollment" -k "AAAAE-AAAAD-AAAAF-AAAAZ-AAAA5"

.. toctree::
