Task Scheduling
---------------

Because a task is an action that can be executed independantly from the others, the way tasks are planified can be from outside. I.e. from another program.
It means that any tasks planification program should do the job. Thus, **Armadito Agent** does not include a task planification system in itself.
Also, frequencies and planification of these tasks are an administrator's choice.

Cron
****

Everything can be done by adding some linux's crontab lines.
An easy script is provided in git repository : `setcrontab.sh <https://github.com/armadito/armadito-agent/blob/DEV/scripts/setcrontab.sh>`_.
It gives an example of a configuration using the multiple calls way previously described.

Armadito Scheduler
******************

**Armadito Scheduler** is an experimental task scheduler for equitable time repartition of tasks over time.



