#!/bin/bash

php_beautifier -r "../*.php" "./armadito-beautified/"
cp -rf ./armadito-beautified/* ../
rm -r ./armadito-beautified
