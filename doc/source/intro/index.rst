Introduction
============

**Armadito for GLPI** aims to be a Free IT management solution dedicated to Antiviruses management. It has to be a complementary solution to `GLPI <http://www.glpi-project.org/?lang=en>`_ : the Free IT and Asset Management Software.
Indeed, GLPI (Gestionnaire Libre de Parc Informatique) is an Information Resource-Manager with an additional Administration Interface. Integrated as a plugin to GLPI, **armadito-glpi** relies on the powerful APIs of GLPI.
The plugin provides many charts grouped on different boards. All these boards are configurable/customizable in order to fit administrator's preferences. Indeed, besides providing useful features, we believes that the formal should not be disregarded. As much as we think that code quality has to be a main concern. Also, this plugin will be constantly passed to sonar scanner with a focus on reducing duplication.

But a management solution is nothing without a multi-platform client side. Also, `FusionInventory agent <https://github.com/fusioninventory/fusioninventory-agent>`_ is fully written in Perl programming language. **armadito-agent** naturally goes the same way, trying to fit in the existing material and environment. But **armadito-agent** is totally independant from pre-installed FusionInventory agent.

.. toctree::

