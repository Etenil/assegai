#!/bin/sh

# Keeping atlatl's mercurial clone up to date.
if [ -d lib/atlatl ]
then
    echo "Updating atlatl... "
    cd lib/atlatl
    hg -q pull
    hg -q up
    cd ../../
    echo "DONE"
else
    echo "Downloading atlatl... "
    curl -s http://atlatl.etenil.net/latest.gz | gunzip - > lib/atlatl.php
    echo "DONE"
fi
