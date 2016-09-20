#!/bin/sh
scriptdir=$(dirname ${0})

rm ${1}/build/logs/log.xml ${1}/build/logs/log.html

set -e

echo "PRE xsltproc"

echo "<testsuites />" | xsltproc \
    --output ${1}/build/logs/log.xml \
    --stringparam basedir ${1} \
    ${scriptdir}/merge.xslt -

echo "POST xsltproc merge"

xsltproc \
    --stringparam projectname ${2} \
    --output ${1}/build/logs/log.html \
    ${scriptdir}/phpunit.log.xslt \
    ${1}/build/logs/log.xml

echo "POST xsltproc phpunit"
