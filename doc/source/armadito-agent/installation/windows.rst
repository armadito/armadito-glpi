Installation on Windows
=======================

Basically, armadito-agent's installation on Windows can be made offline or online.
It means at least two different kind of installers. We provide command line installation examples for both installers.
We decided to use **Inno Setup** also because it is quite simple, open source and well documented.
Also, it is a good alternative to Perl Dev Kit (now not available for individual sales) and old school installations with cpan.


.. warning:: Inno Setup is now well known, but some Antiviruses still have false positives, for example with temporary files created by Inno Setup Installer.


About Enrollment Key(s)
-----------------------

In both cases, installation can be done without providing a good enrollement key, while it is not recommended.
Administrators should be able to fastly generate enrollment key(s) from Armadito plugin for GLPI from menu General > Enrollment Keys.

**If you installed without a valid enrollment key**, no worry, you will still be able to enroll later.
To do so, you can type in a command prompt (with admin rights) :

::

   $ armadito-agent -t "Enrollment" -k "AAAAE-AAAAD-AAAAF-AAAAZ-AAAA5"


Offline installer
-----------------

Offline installer's size is bigger (~80MB) because it includes a strawberry perl distribution and all perl dependencies.

Batch example :

::

   @ECHO OFF
   set version=0.1.0_02
   set programpath=%~dp0\..

   %programpath%\out\Armadito-Agent-%version%-Setup-Offline.exe ^
    /SP- /VERYSILENT /LOG=%programpath%\out\setuplog.txt /KEY=AAAAE-AAAAD-AAAAF-AAAAZ-AAAA5


Online installer
----------------

Whereas Online installer is smaller (~0.5MB) but downloads dependencies at installtime on a cpan mirror.
Furthermore, it allows agent to use already installed local perl distribution.
Note that you can use a custom cpan mirror with that installer.
CPAN proxy configuration has also been simplified.

Batch example :

::

   @ECHO OFF
   set version=0.1.0_02
   set programpath=%~dp0\..

   %programpath%\out\Armadito-Agent-%version%-Setup-Online.exe ^
    /SP- /VERYSILENT /LOG=%programpath%\out\setuplog.txt /KEY=AAAAE-AAAAD-AAAAF-AAAAZ-AAAA5 /PERLPATH=C:\strawberry


.. note:: Because it is open-source, and because we provide packaging scripts, you can create custom installers by yourself.

.. toctree::
