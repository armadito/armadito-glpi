Scans
=====

Board
-----

Deployed scan results are stored in GLPI database.
Also, some charts gives an overview of what has been successful or not.
Scan durations can be compared to each other.
It leads to a better management of future scans, because you are able to know how long a specific scan take in your infrastructure,

For now, there is three charts :

* **ScanStatusChart** : shows deployed scans statuses' repartition
* **showLongestScansChart** : shows top ten longest scans
* **showAverageDurationsChart** : shows average duration by scan configuration

Scans
-----

This submenu gives details about deployed scans. It includes a link to the scan configuration used.
It gives you once again a search engine for selecting scans.
Furthermore, it can be useful to know which kind of scan leads to more threat detections.

Configurations
--------------

This part is important because it is needed for deploying a new scan.
It's the single place where you set AV specific's scan options.
You can select an enrolled Antivirus, define where to scan and with which parameters.

**Example** :

You want to create an on-demand scan configuration for agents that have ArmaditoAV installed :

**1** - You click on "+" on the top at left

**2** - You fill form according to your needs

**3** - You validate
