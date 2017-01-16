Build on Windows
================

Prerequisites
-------------

* Git client
* Perl > 5.8 (`Strawberry Perl <http://strawberryperl.com/>`_ or something else)
* Perl module **inc::Module::Install**

Modules
~~~~~~~
::

   $ cpan install inc::Module::Install
   $ cpan install App::Cpanminus

.. note:: You might have to **force** Authen::Simple installation due to a known issue  : https://rt.cpan.org/Public/Bug/Display.html?id=100750

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
   $ dmake
   $ dmake test
   $ dmake install

.. note:: **dmake** is the make utility installed within Strawberry Perl. It may be **nmake** instead with an other Perl environment.

.. toctree::
