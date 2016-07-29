Introduction
============

Armadito for GLPI aims to be a Free IT management solution dedicated to Antiviruses management. It has to be a complementary solution to `GLPI <http://www.glpi-project.org/?lang=en>`_ : the Free IT and Asset Management Software.
Indeed, GLPI (Gestionnaire Libre de Parc Informatique) is an Information Resource-Manager with an additional Administration Interface. Integrated as a plugin to GLPI, **armadito-glpi** relies on the powerful APIs of GLPI. Thus, most of the work is based on existing because it is not worth to reinvent the wheel. By the way, this plugin reuses a lot of work done in the `FusionInventory plugin for GLPI <https://github.com/fusioninventory/fusioninventory-for-glpi>`_. Consequently, like **fusioninventory-for-glpi**, **armadito-glpi** is licensed under the `AGPLv3+ <https://www.gnu.org/licenses/license-list.html#AGPL>`_.

But a management solution is nothing without a multi-platform client side. Also, `FusionInventory agent <https://github.com/fusioninventory/fusioninventory-agent>`_ is fully written in Perl programming language. **armadito-agent** naturally goes the same way, trying to fit in the existing material and environment. It is why **armadito-agent** depends heavily on the previously installed FusionInventory agent. Because FusionInventory agent brings with it a minimal version of `Strawberry Perl <http://strawberryperl.com/>`_ on Windows, **armadito-agent** reuses that Perl environment in addition to the FusionInventory Agent classes. Reusing these works leads to the actual licensing of **armadito-agent** under the `GPLv2+ <https://www.gnu.org/licenses/license-list.html#GNUGPLv2>`_.

Armadito for GLPI main features for the first versions will be the following :

* Agents viewing and management from Admin Interface
* Antiviruses and virus databases states viewing from Admin Interface
* Antiviruses alerts viewing from Admin Interface
* Antiviruses scans viewing from Admin Interface

.. toctree::

