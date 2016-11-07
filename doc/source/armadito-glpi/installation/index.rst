Installation
============

For now, you can only install it by downloading sources from github.
Later on, the plugin should be on `GLPI plugins <http://plugins.glpi-project.org/#/>`_.

Prerequisites
-------------

* Git client
* GLPI installed >= 9.1

Instructions
------------

Go into GLPI plugins directory, then get the lastest version of plugin sources on github :
::

   cd WEBSERVER_DIR/glpi/plugins/
   git clone -b DEV https://github.com/armadito/armadito-glpi armadito


Then, after logging into GLPI you should be able to install and enable Armadito Plugin. To do this, go to **Setup > Plugins** and select Armadito.


.. danger:: Using the plugin before the official release means that your plugin database is not guaranted to be perfectly migrated. Also, this is a **DEVELOPER ONLY** version.
