#!/bin/bash

set -e

if [[ $TRAVIS_BRANCH == 'coverity_scan' ]]
then
	cp -r /home/travis/build/armadito/armadito_lib /home/travis/build/armadito/glpi/plugins/armadito/lib
fi

ant -Dclearsavepoint='true' -Dbasedir=. -f ./phpunit/build.xml phpunit.all
