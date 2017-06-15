#!/bin/bash
#
if [ "$1" != "" ]; then
  echo "Dir is $1"
else
  echo "Positional parameter 1 is empty"
  exit;
fi

for map in $( find $1/_maps -type f -name '*.dmm'); do
  echo $map
  cp -f $map $map.backup
done

echo -en "0-3,5-122\n\n" | python3 $1/tools/mapmerge/mapmerger.py $1/_maps 1;