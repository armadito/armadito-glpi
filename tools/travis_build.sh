#!/bin/bash

set -e

if [[ $TRAVIS_BRANCH == 'coverity_scan' ]]
then
	cp -r /home/travis/build/armadito/glpi/plugins/_lib /home/travis/build/armadito/glpi/plugins/armadito/lib
fi

ant -Dclearsavepoint='true' -Dbasedir=. -f ./glpi/plugins/armadito/phpunit/build.xml phpunit.all

