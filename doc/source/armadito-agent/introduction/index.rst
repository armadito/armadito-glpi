Introduction
============

**Armadito Agent** is the interface between Armadito plugin for GLPI and Antiviruses installed on managed computers.
Note that this agent is not dedicated to only manage Armadito Antivirus.
This agent basically provides a set of tasks that can be executed at any time :

* **Enrollment** : new agent ID assignation or re-assignation.
* **GetJobs** : get available jobs for this agent.
* **RunJobs** : run jobs with a given priority and post results to GLPI.
* **State** : get antivirus status and send it to GLPI.
* **Scan** : on-demand scan and send results to GLPI.
* **Alerts** : check for virus alerts and send it to GLPI.


Provided Tasks
--------------

It is important that administrators understand briefly how these tasks works.
Thus, in this section, all tasks will be described in details.

Enrollment
**********

-- POST **/api/agents**

Getjobs
*******

-- GET **/api/jobs**

Runjobs
*******

-- POST **/api/jobs**

State
*****

-- POST **/api/states**

Scan
****

-- POST **/api/scans**

Alerts
******

-- POST **/api/alerts**

Tasks Scheduling
----------------

Considering that a task is an action that can be executed independantly from the others, the way tasks are planified has to be from outside. I.e. from another program.
It means that any tasks planification program should do the job. Thus, **Armadito Agent** does not include a task planification system in itself.
Also, frequencies and planification of these tasks are an administrator's choice.


Crontab
*******

Everything can be done by adding some linux's crontab lines.

Armadito Scheduler
******************

**Armadito Scheduler** is an experimental task scheduler for equitable time repartition of tasks over time.



