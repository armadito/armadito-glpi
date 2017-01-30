.. Armadito-glpi documentation master file, created by
   sphinx-quickstart on Tue Apr 12 17:35:15 2016.

.. image:: glpi_logo.png
   :height: 96px
   :width: 96px
   :alt: logo glpi
   :align: left

=================
Armadito for GLPI
=================

Armadito for GLPI is an open-source solution to manage computer antiviruses on IT infrastructures.
It could be used to manage various kind of Antiviruses.

**Key points** :

* **Open source**
* **Multi Antiviruses**
* **Multi Operating Systems**
* **Multi Task schedulers**


**Main features** :

* **Scan** : on-demand scan deployment on computers
* **Alerts** : centralization of alerts (realtime + on-demand scans)
* **AVConfig** : centralized remote configuration
* **State** : databases updates and antivirus states

**Compatibility matrix** :

+-------------+-----------+----------+------+--------+----------+-------+
| Antivirus   | OS        | Versions | Scan | Alerts | AVConfig | State |
+=============+===========+==========+======+========+==========+=======+
| Armadito    | Linux     | 0.12.8   | Yes  | Yes    | Soon     | Yes   |
+-------------+-----------+----------+------+--------+----------+-------+
| Kaspersky   | Windows   | 17.0.0   | Yes  | Yes    | Yes      | Yes   |
+-------------+-----------+----------+------+--------+----------+-------+
| ESET Nod32  | Linux     | 4.0      | Yes  | Yes    | Soon     | Yes   |
+-------------+-----------+----------+------+--------+----------+-------+
| Avast       | Windows   |          | Soon | Soon   | Soon     | Soon  |
+-------------+-----------+----------+------+--------+----------+-------+
| 360TotalSec | Windows   |          | Soon | Soon   | Soon     | Soon  |
+-------------+-----------+----------+------+--------+----------+-------+
| ...         | ...       |  ...     | ...  | ...    | ...      | ...   |
+-------------+-----------+----------+------+--------+----------+-------+

This solution can be divided into two subprojects :

* **armadito-agent** : client-side part installed on each managed computer.
* **armadito-glpi**  : server-side part developed as a plugin for GLPI.

.. toctree::
   :maxdepth: 2

   intro/index.rst
   armadito-agent/index.rst
   armadito-glpi/index.rst
   api/index.rst
   licensing/index.rst
   screenshots/index.rst
..   faq/index.rst


