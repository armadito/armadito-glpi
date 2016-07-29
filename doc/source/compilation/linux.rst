Build on Linux
==============

Prerequisites
-------------

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

:: 

   $ perl Makefile.PL
   $ make
   $ make test
   $ make install

.. toctree::
