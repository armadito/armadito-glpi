#!/bin/bash

set -e

if [[ $TRAVIS_BRANCH == 'coverity_scan' ]]
  cp -r /home/travis/build/armadito/glpi/plugins/_lib /home/travis/build/armadito/glpi/plugins/armadito/lib 

ant -Dclearsavepoint='true' -Dbasedir=. -f ./glpi/plugins/armadito/phpunit/build.xml phpunit.all
