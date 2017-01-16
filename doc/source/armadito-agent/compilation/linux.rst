Build on Linux
==============

Prerequisites
-------------

* Git client
* GNU make
* C compiler
* Perl > 5.8

Ubuntu
~~~~~~
::

   $ sudo apt-get install libmodule-install-perl cpanminus


Instructions
------------

Get the last version of Armadito Agent sources on github :
::

   $ git clone -b DEV https://github.com/armadito/armadito-agent


To install all dependencies automatically :
::

   $ cpanm --quiet --installdeps --no-test .


Then, at root of project's sources :
::

   $ perl Makefile.PL
   $ make
   $ make test
   $ make install

.. toctree::
