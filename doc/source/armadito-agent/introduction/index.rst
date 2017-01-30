Introduction
============

**Armadito Agent** is the interface between Armadito plugin for GLPI and Antiviruses installed on managed computers.
Note that this agent will not only be dedicated to manage Armadito Antivirus.
This agent basically provides a set of tasks that can be executed at any time :

* **Enrollment** : new agent ID assignation or re-assignation.
* **GetJobs** : get available jobs for this agent.
* **RunJobs** : run jobs with a given priority and post results to GLPI.
* **State** : get antivirus status and send it to GLPI.
* **Scan** : on-demand scan and send results to GLPI.
* **Alerts** : check for virus alerts and send it to GLPI.
* **AVConfig** : manage antiviruses' configurations

.. note:: You can also find a fully detailed documentation on `CPAN <http://search.cpan.org/search?query=Armadito-Agent&mode=all>`_.

CPAN documentation also includes binary usage description that you can also get by using *--help* option on computer where agent is installed.


