#!/bin/bash

# Keeping atlatl's mercurial clone up to date.
if [ -d lib/atlatl ]
then
    echo -n "Updating atlatl... "
    cd lib/atlatl
    hg -q pull
    hg -q up
    cd ../../
    echo "DONE"
else
    echo -n "Downloading atlatl... "
    curl -s http://atlatl.etenil.net/latest.gz | gunzip - > lib/atlatl.php
    echo "DONE"
fi
