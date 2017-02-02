Installation on Linux
=====================

A PPA is provided for Ubuntu in order to simplify installation.

.. note:: For other distributions, installation can always be done by following instructions given in *Build from Sources* previous section of this documentation.

Ubuntu with PPA
---------------

Currently available for :

* xenial (16.04LTS)
* trusty (14.04LTS)

To install :
::

   $ sudo add-apt-repository ppa:armadito/armadito-av
   $ sudo apt-get update
   $ sudo apt-get install libarmadito-agent-perl


After installation, you can enroll your computer :
::

   $ sudo armadito-agent -t "Enrollment" -k "AAAAE-AAAAD-AAAAF-AAAAZ-AAAA5"


For further informations about PPA, see `Launchpad.net <https://launchpad.net/~armadito/+archive/ubuntu/armadito-av>`_

.. toctree::
