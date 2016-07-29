Build on Windows
================

Prerequisites
-------------

* Git client
* Perl > 5.8 (`Strawberry Perl <http://strawberryperl.com/>`_ or something else)
* Perl module **inc::Module::Install**
* Perl module **FusionInventory::Agent** > 2.3.17 (to be tested on older versions)


FusionInventory Agent
~~~~~~~~~~~~~~~~~~~~~

Simply follow the `Installation tutorial <http://fusioninventory.org/documentation/agent/installation/windows/>`_

.. warning:: You **can't** install **FusionInventory Agent** from **cpan** on Windows because it works only on Linux.

Modules
~~~~~~~
::   

   cpan install inc::Module::Install
   cpan install -f Authen::Simple 

.. note:: You might have to **force** Authen::Simple installation due to a known issue  : https://rt.cpan.org/Public/Bug/Display.html?id=100750

Instructions
------------

Get the last version of Armadito Agent sources on github :
::

   git clone -b DEV https://github.com/armadito/armadito-agent

Then, at root of project's sources :
::

   $ perl Makefile.PL
   $ dmake
   $ dmake test
   $ dmake install

.. note:: **dmake** is the make utility installed within Strawberry Perl. It may be **nmake** instead with an other Perl environment.

.. toctree::
