Getting Started
===============

Armadito Plugin for GLPI does not radically differs in usage from core GLPI.
It is why people who already use GLPI should not be lost when using that plugin.

For this tutorial, we assume that you have GLPI >= 9.1 already installed and configured.
If not, please follow the procedure on `glpi-project.org <http://glpi-project.org/spip.php?article61>`_.

1. Plugin Installation
~~~~~~~~~~~~~~~~~~~~~~

* :doc:`Tutorial  </armadito-glpi/installation/index>`

2. Enrollment Keys generation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To create a new enrollment key, go to **General** > **Enrollment Keys** menu in the plugin.

**1** - Click on "+" button on the top at left

**2** - Select an expiration date and the usage counter

**3** - Validate form by clicking on add button

3. Agents installation
~~~~~~~~~~~~~~~~~~~~~~

Please, follow one of these tutorials :

* :doc:`Linux Tutorial  </armadito-agent/installation/linux>`
* :doc:`Windows Tutorial  </armadito-agent/installation/windows>`

4. Agents configuration
~~~~~~~~~~~~~~~~~~~~~~~

Basically, you need to set a few things in **agent.cfg** the first time you install an agent :

**1** - Plugin Server URL (ex: http://127.0.0.1/glpi/plugins/armadito/)

**2** - An antivirus to manage (ex: Armadito, Kaspersky, Eset)

**3** - The kind of scheduler you will use (ex: Cron, Win32Native)

**4** - Your network advanced configuration (if needed, i.e. proxy, authentification, etc)

Furthermore, you should set a scheduling configuration that fits your needs in **scheduler-X.cfg**.

More info :

* :doc:`Configuration doc  </armadito-agent/configuration/index>`
* :doc:`Scheduling doc  </armadito-agent/scheduling/index>`

5. Agents enrollment
~~~~~~~~~~~~~~~~~~~~

On either Linux or Windows, you need admin/superuser rights for enrollment.
It is due to the fact that armadito-agent retrieves your system UUID as an unique identifier.
Indeed, it allows automatic association with inventory in GLPI.

Be sure that you got a valid enrollment key, or you won't be able to enroll your agent.

To enroll/re-enroll an agent, just type the following with **admin/superuser** rights :

::

   $ armadito-agent -t "Enrollment" -k "AAAAE-AAAAD-AAAAF-AAAAZ-AAAA5"

.. note:: Feel free to ask on `forum.armadito.org <https://forum.armadito.org>`_ if you have any question.

