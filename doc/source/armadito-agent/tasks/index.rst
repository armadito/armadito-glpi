Provided Tasks
--------------

It is important that administrators understand briefly how these tasks works.
Thus, in this section, all tasks will be described in details.

Enrollment
**********

-- POST **/api/agents** :

**1** - After retrieving computer's UUID, the agent sends a request containing enrollment key to add or update agent in database.

**2** - Enrollment key validity is checked on server side.

**3** - An ID from **0** to **number_of_agents** is assigned and sent back to agent if enrollment is authorized.

**4** - Then the agent stores persistently this ID.

Enrollment is mandatory and need to be done when agent is not in database (removed or first time). If your computer changes his UUID, a new agent ID will be assigned when running this task again. It means that the previous ID will still be in database. It is then administrator's choice to keep it or remove it by using plugin's web interface.

**Example** :
::

    $ armadito-agent -t "Enrollment" -k "AAAAA-AAAAA-AAAAA-AAAAA-AAAAA"


Scheduler
*********

-- GET & POST **/api/schedulers**

**1** - Retrieve Scheduler configuration defined into plugin Armadito for GLPI (if there is one)

**2** - Update local Scheduler configuration (if needed)

**3** - Retrieve current local Scheduler configuration

**4** - Send it in a json message to plugin Armadito for GLPI

**5** - Store only differences in database


**Example** :
::

    $ armadito-agent -t "Scheduler"


Getjobs
*******

-- GET **/api/jobs** :

**1** - Enrolled agent sends a request to get assigned jobs.

**2** - The plugin sends back an array of jobs in a json message.

**3** - The agent parses the message and stores it for later use.

There is a getJobs limit which defines the maximum of jobs that can be retrieved for each request to API.
By default, it is limited to 10 jobs but you can change this value in **General** > **Configuration** > **Jobs** from Armadito plugin in GLPI.

**Example** :
::

    $ armadito-agent -t "Getjobs"

Runjobs
*******

-- POST **/api/jobs**

**1** - Get list of previously stored jobs (locally)

**2** - Execute these jobs sequentially

**3** - Sends Jobs execution statuses to GLPI. It can includes error messages.

A Job can have 4 differents levels of priority :

* low    = 0
* medium = 1
* high   = 2
* urgent = 3


Job priority can be selected by administrator when creating a new job in Armadito Plugin for GLPI.
Note that, at each call of **Runjobs**, jobs are executed sequentially according to their priority level.

Example 1, tasks are executed after waiting 10 seconds :
::

    $ armadito-agent -t "Runjobs" -w 10


Example 2, tasks are executed after waiting randomly between 0 and 10 seconds :
::

    $ armadito-agent -t "Runjobs" -wr 10


.. note:: It works in combination with **Getjobs** task. **Getjobs** should be run more often than **Runjobs** in order to fully benefit from job priority system.

State
*****

-- POST **/api/states**

**1** - Get Antivirus status (databases update status, on-access activation, etc)

**2** - Sends this status in a json message to Armadito plugin for GLPI.

**3** - Database is updated in GLPI

For Armadito Antivirus, the first step consists on sending a GET request to RESTful API of the antivirus.

**Example** :
::

    $ armadito-agent -t "State"

Scan
****

-- POST **/api/scans**

**1** - Ask Antivirus for a new on-demand scan

**2** - Send progress regularly to GLPI before scan's end (optional)

**3** - Send scan results to plugin Armadito for GLPI

**Example** :
::

    $ armadito-agent -t "Scan"

Alerts
******

-- POST **/api/alerts**

**1** - Retrieve Antivirus specific alerts

**2** - Send alerts in a json message to plugin Armadito for GLPI

**3** - Store these alerts in database

**Example** :
::

    $ armadito-agent -t "Alerts"


.. note:: A checksum considering main characteristics is computed on server side. It allows to avoid inserting duplicates.

AVConfig
********

-- GET & POST **/api/avconfigs**

**1** - Retrieve Antivirus configuration defined into plugin Armadito for GLPI (if there is one)

**2** - Update local computer Antivirus' configuration (if needed)

**3** - Retrieve current local Antivirus configuration

**4** - Send it in a json message to plugin Armadito for GLPI

**5** - Store only differences in database


**Example** :
::

    $ armadito-agent -t "AVConfigs"

.. note:: The way Step **5** has been implemented leads to a great reduction of database size. Indeed, if 1000 agents have the same configuration, only 1 copy will be stored.



