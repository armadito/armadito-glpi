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
It basically simplify the use of schtasks in the agent specific case.
For full documentation on possibilities offered by Schtasks, see on `MSDN Schtasks.exe <https://msdn.microsoft.com/en-us/library/windows/desktop/bb736357(v=vs.85).aspx>`_.

* **Configuration example** : `scheduler-win32native.cfg <https://github.com/armadito/armadito-agent/blob/DEV/etc/scheduler-win32native.cfg>`_
* **Configuration file path** : <installdir>\\etc\\scheduler-win32native.cfg
* **Source File** : `Scheduler/Win32Native.pm <https://github.com/armadito/armadito-agent/blob/DEV/lib/Armadito/Agent/Scheduler/Win32Native.pm>`_

Cron
****

Cron simply add/update a crontab configuration file for Armadito Agent.
For full documentation of crontab, see `CronHowto <https://help.ubuntu.com/community/CronHowto>`_.

* **Configuration example** : `scheduler-cron.cfg <https://github.com/armadito/armadito-agent/blob/DEV/etc/scheduler-cron.cfg>`_
* **Configuration file path** : <installdir>/etc/scheduler-cron.cfg
* **Source File** : `Scheduler/Cron.pm <https://github.com/armadito/armadito-agent/blob/DEV/lib/Armadito/Agent/Scheduler/Cron.pm>`_


In order to apply a new/updated configuration :

::

    armadito-agent -t "Scheduler"


Armadito Scheduler
******************

.. warning:: It is still under experimentation.

**Armadito Scheduler** is a task scheduler conceived with the purpose of maximizing equitability in the repartition of tasks over time.

* **Source Code** : `Github Armadito-Scheduler <https://github.com/armadito/armadito-scheduler>`_
