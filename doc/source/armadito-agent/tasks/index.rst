Provided Tasks
--------------

It is important that administrators understand briefly how these tasks works.
Thus, in this section, all tasks will be described in details.

Enrollment
**********

-- POST **/api/agents** :

**1** - After retrieving computer's UUID, the agent sends a request to add or update agent in database.

**2** - An ID from **0** to **number_of_agents** is assigned and sent back to agent.

**3** - Then the agent stores persistently this ID.

Enrollment is mandatory and need to be done when agent is not in database (removed or first time). If your computer changes his UUID, a new agent ID will be assigned when running this task again. It means that the previous ID will still be in database. It is then administrator's choice to keep it or remove it by using plugin's web interface.

**Example** :
::

    armadito-agent -t "Enrollment"

Getjobs
*******

-- GET **/api/jobs** :

**1** - The agent sends a request to get assigned jobs.

**2** - The plugin sends back an array of jobs in a json message.

**3** - The agent parses the message and stores it for later use.

There is a getJobs limit which defines the maximum of jobs that can be retrieved for each request to API.
By default, it is limited to 10 jobs but you can change this value in **General** > **Configuration** > **Jobs** from Armadito plugin in GLPI.

**Example** :
::

    armadito-agent -t "Getjobs"

Runjobs
*******

-- POST **/api/jobs**

**1** - Get list of previously stored jobs

**2** - Execute these jobs

**3** - Sends Jobs execution statuses to GLPI. It can includes error messages.

A Job can have 4 differents levels of priority :

* low    = 0
* medium = 1
* high   = 2
* urgent = 3

There is at least two ways to use this priority system :

* By a single call, jobs are executed one after another according to their priority level.

**Example** :
::

    armadito-agent -t "Runjobs"

OR

* Multiple calls, but only jobs for a single priority level are executed at a time.

In the following example, "urgent" tasks will be executed after waiting 5 seconds, "low" priority tasks after 30 seconds.

**Example** :
::
    armadito-agent -t "Runjobs" -p 3 -w 5
    armadito-agent -t "Runjobs" -p 2 -w 10
    armadito-agent -t "Runjobs" -p 1 -w 15
    armadito-agent -t "Runjobs" -p 0 -w 30

.. note:: Cron, Armadito Scheduler or any other task scheduling solution can use one of these ways for executing jobs.

State
*****

-- POST **/api/states**

**1** - Get Antivirus status (databases update status, on-access activation, etc)

**2** - Sends this status in a json message to Armadito plugin for GLPI.

**3** - Database is updated in GLPI

For Armadito Antivirus, the first step consists on sending a GET request to RESTful API of the antivirus.

**Example** :
::

    armadito-agent -t "State"

Scan
****

-- POST **/api/scans**

**1** - Ask Antivirus for a new on-demand scan

**2** - Send progress regularly to GLPI before scan's end (optional)

**3** - Send scan results to plugin Armadito for GLPI

**Example** :
::

    armadito-agent -t "Scan"

Alerts
******

-- POST **/api/alerts**

**1** - Read MAX_ALERTS inside specified alerts directory

**2** - Send alerts in a json message to plugin Armadito for GLPI

**3** - Store these alerts in database

**Example** :
::

    armadito-agent -t "Alerts" --alert-dir /var/spool/armadito --max-alerts 10
