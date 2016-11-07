Introduction
============

**Armadito Agent** is the interface between Armadito plugin for GLPI and Antiviruses installed on managed computers.
Note that this agent is not dedicated to only manage Armadito Antivirus.
This agent basically provides a set of tasks that can be executed at any time :

* **Enrollment** : new agent ID assignation or re-assignation. -- POST **/api/agents**
* **GetJobs** : get available jobs for this agent. -- GET **/api/jobs**
* **RunJobs** : run jobs with a given priority and post results to GLPI. -- POST **/api/jobs**
* **State** : get antivirus status and send it to GLPI. -- POST **/api/states**
* **Scan** : on-demand scan and send results to GLPI. -- POST **/api/scans**
* **Alerts** : check for virus alerts and send it to GLPI. -- POST **/api/alerts**


Provided Tasks
--------------

It is important that administrators understand briefly how these tasks works.
Thus, in this section, all tasks will be described in details.

Enrollment
**********

Getjobs
*******

Runjobs
*******

State
*****

Scan
****

Alerts
******

Tasks Scheduling
----------------

Considering that a task is an action that can be executed independantly from the others, the way tasks are planified has to be from outside. I.e. from another program.
It means that any tasks planification program should do the job. Thus, **Armadito Agent** does not include a task planification system in itself.
Also, frequencies and planification of these tasks are an administrator's choice.


Crontab
*******

Armadito Scheduler
******************

