#!/bin/bash
# NewLines(before=if:switch:while:for:foreach:function:T_CLASS:return:break,after=T_COMMENT:return)

php_beautifier --filters "Pear()" -r "../*.php" "./armadito-beautified/" 
cp -rf ./armadito-beautified/* ../
rm -r ./armadito-beautified
