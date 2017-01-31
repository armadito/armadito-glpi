General
=======

This menu regroup things that are global to the whole plugin.
It includes a global configuration section.

Board
-----

The focus of this board is mainly about armadito agents management.
That submenu is the main board of the plugin. Like all plugins' boards, it will be fully configurable.

For now, there is three charts :

* **AntivirusChart** : shows antiviruses repartition of enrolled agents.
* **ComputersChart** : shows armadito agents repartition in comparison to GLPI computers.
* **LastContactsChart** : shows agents connexions of last hours

Agents
------

This submenu should be used frequently because it is the main interface for managing agents.
Basically, it is a configurable list of enrolled agents.

The first thing to understand is that you can easily search and select group of agents by this web page.
It uses one of the most powerful features provided by GLPI project's core for manipulating objects you have in the web console.
It is implemented and used in GLPI since a long time. It allows you to perform simple to very complex database requests.
Then, once you have selected some agents, you can perform what GLPI calls "massive actions" on them.

**Example** :

You want to deploy an on-demand scan on agents that have Kaspersky installed.

**1** - You configure the search engine in order that only agents with Antivirus Name containing "Kaspersky" are selected.

**2** - You select all agents that are now listed

**3** - You click on action and select *Scan*

**4** - You select a scan configuration (previously created)

**5** - You select a job priority and that's all.

Antiviruses
-----------

This submenu is a configurable list of all antiviruses enrolled until now.
Some massive actions could be added here in future versions.

Enrollment Keys
---------------

This submenu is where you can manage your enrollment keys. You can create/revoke enrollment keys.
Before doing anything else in the plugin, you have to create one or more enrollment key(s).

If you are not new to GLPI, you probably found by yourself how to add a new key.
Indeed, the procedure is quite simple :

**1** - Click on "+" button on the top at left

**2** - Select an expiration date and the usage counter

**3** - Validate form by clicking on add button

To revoke enrollment keys :

**1** - Select one or more keys

**2** - Select "put in dustbin" Action and validate

.. note:: Note that whenever you use "put in dustbin" action in GLPI, you can use "restore" action. It is possible because it is a simple flag in database.

**Usage counter** is decremented at each newly enrolled agent. It can be really helpful if you want to keep a true control of enrollment process in your infrastructure.


Configuration
-------------

That menu regroup configurations for the whole plugin. It allows you to rapidly check what configuration variables are available.

* :doc:`Configuration  </armadito-glpi/configuration/index>`


