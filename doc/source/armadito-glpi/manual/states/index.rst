States
======

Board
-----

That board has been implemented to simplify the way you can visualize your antiviruses.
Each agent send regularly databases updates statuses.
The provided charts are here to help you detecting which agents/antiviruses might have problems updating their databases.

The plugin provides some default charts but we really think that ussers feedback will help improve this kind of board.
Users feedback should give an idea of what are the best indicators, of what really need administrators. All ideas are of course welcomed.

For now, there is three charts :

* **UpdateStatusChart** : shows databases updates statuses repartition
* **MostCriticalUpdatesChart** : shows top 10 agents with most critical updates
* **LastUpdatesChart** : shows databases updates of last hours

States
------

Similar to **General** > **Agents**, this submenu allows you to search and select agents.
But this time, it is focused on databases updates management.
And it gives you a simple way to be sure that everything is going well, that every antiviruses' databases are up-to-date.
Most antiviruses basically split their database updates into multiple sub parts (or modules).
Also, a link inside each row allows you to check databases updates in details.

Furthermore, you probably want to be sure that antiviruses' real time protection is activated.
Also, a section called "AV Details" refers to every statuses information related to an installed Antivirus. It includes real time protection status for example.
With some Antiviruses, it means a lot of details. It is why we called this menu "States". It does not only consider databases updates.


AV Configurations
-----------------

This part is about controlling your antiviruses' configuration.
It implies a large data set to store into GLPI database.
Because GLPI uses MySQL, the implementation on how are stored configurations is very specific.
To be able to gather data from big infrastructures, a custom Entity–attribute–value model has been implemented.
Indeed, we think that most configurations over a same IT infrastructure are most of the time very similar, even sometimes identical.
Also, it is not worth to store identical data multiple times.
It leads to a great reduction of database size usage.
