Task Scheduling
---------------

Because a task is an action that can be executed independantly from the others, the way tasks are planified can be from outside. I.e. from another program.
It means that any tasks planification program should do the job. Thus, **Armadito Agent** does not include a task planification system in itself, but provides wrappers.

To allow easy remote control of agent's tasks, armadito plugin for GLPI provides some ways to configure schedulers from server side.
Indeed, managing these planifications can be done within "Scheduler" agent task :

::

    armadito-agent -t "Scheduler"


That task is a kind of wrapper for OS Schedulers, but it can communicates with Armadito Plugin for GLPI's RESTful API.
Administrator could use these wrappers, but he can also decide to manage scheduling by himself from outside.


If Administrator decides to use Armadito Agent's wrappers, it has to be configured in main configuration file (etc/agent.cfg).
It can be done, by simply setting which scheduler to use :

::

    scheduler = Cron

Then, scheduler has to be configured properly. Each wrapper has a specific configuration file associated (etc/scheduler-win32native.cfg, etc/cron.cfg, etc).
Because constraints in IT infrastructures differs from an organization to an other, frequencies and planification of these tasks are an administrator's choice.

Win32Native
***********

Win32Native is a wrapper to the native "SCHTASKS" program of Windows.

Cron
****

Cron simply add/update a crontab configuration file for Armadito Agent.

Armadito Scheduler
******************

**Armadito Scheduler** is a task scheduler conceived for maximize equitability in the repartition of tasks over time.

