#!/bin/bash

cd ../js
for line in $(find . -name '*.js' ! -name '*min.js' ); do 
     echo "$line"
     curl -X POST -s --data-urlencode 'input@'$line https://javascript-minifier.com/raw > ${line::-3}.min.js
done

#curl -X POST -s --data-urlencode 'input@ready.js' https://javascript-minifier.com/raw > ready.min.js
