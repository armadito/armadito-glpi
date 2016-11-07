Introduction
============

**Armadito for GLPI** aims to be a Free IT management solution dedicated to Antiviruses management. It has to be a complementary solution to `GLPI <http://www.glpi-project.org/?lang=en>`_ : the Free IT and Asset Management Software. Indeed, GLPI is an Information Resource-Manager with an additional Administration Interface.

Integrated as a plugin to GLPI, **armadito-glpi** relies on the powerful APIs of GLPI.
The plugin provides many charts grouped on different boards. All these boards are configurable/customizable in order to fit administrator's preferences. Indeed, besides providing useful features, we believe that the formal should not be disregarded. As much as we think that code quality has to be a main concern. Also, this plugin will be constantly passed to sonar scanner with a focus on keeping duplication rate as low as possible. Besides, SonarQube results are publicly available on `sonarqube.com <https://sonarqube.com/dashboard?id=armadito%3Aglpi>`_.

**Armadito Agent** is the client side program which communicates to both Antiviruses and GLPI. Note that all communcations with the plugin are passing by a fully documented `RESTful API <http://petstore.swagger.io/?url=https://raw.githubusercontent.com/armadito/armadito-glpi/DEV/api/swagger.yaml#/>`_. Because `FusionInventory agent <https://github.com/fusioninventory/fusioninventory-agent>`_ is fully written in Perl programming language, **armadito-agent** naturally goes the same way, trying to fit in the existing material and environment. But **armadito-agent** is totally independant from any pre-installed FusionInventory agent. For maintaining code quality, perl-critic is passed on each build, including on `travis-ci <https://travis-ci.org/armadito/armadito-glpi>`_. Note that except during Enrollment for retrieving Computer's UUID, **armadito-agent** does not need to be run as superuser.

.. toctree::

