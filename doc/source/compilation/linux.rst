Build on Linux
==============

Prerequisites
-------------

* Git client
* GNU make
* C compiler
* Perl > 5.8
* Perl module FusionInventory::Agent > 2.3.5+ (to be tested on older versions)

Ubuntu 
~~~~~~
:: 

   $ sudo apt-get install libmodule-install-perl fusioninventory-agent 


Instructions
------------

Get the last version of Armadito Agent sources on github :
::

   git clone -b DEV https://github.com/armadito/armadito-agent


Then, at root of project's sources :
::

   $ perl Makefile.PL
   $ make
   $ make test
   $ make install

.. toctree::
