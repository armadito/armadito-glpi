Introduction
============

**Armadito for GLPI** aims to be a Free IT management solution dedicated to Antiviruses management. It is a complementary solution to `GLPI <http://www.glpi-project.org/?lang=en>`_ : the Free IT and Asset Management Software. Indeed, GLPI is an Information Resource-Manager with an additional Administration Interface.

Integrated as a plugin to GLPI, **armadito-glpi** relies on the powerful APIs of GLPI.
The plugin provides many charts grouped on different boards. All these boards are configurable/customizable in order to fit administrator's preferences. This plugin is all about the capacity of simply managing multiple kind of antiviruses. Development is driven with that goal since the beginning. Also, it should be easier to manage multiple antiviruses.

**Armadito Agent** is the client side program which communicates to both Antiviruses and GLPI. Note that all communications with the plugin are within a `RESTful API <http://petstore.swagger.io/?url=https://raw.githubusercontent.com/armadito/armadito-glpi/DEV/api/swagger.yaml#/>`_. Armadito agent is written in oriented object programming in Perl language. It has been conceived in a way that simplify being compatibile with new antiviruses. For maintaining code quality, perl-critic is passed on each build, including on `travis-ci <https://travis-ci.org/armadito/armadito-glpi>`_.

.. toctree::

