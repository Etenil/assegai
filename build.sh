#!/bin/sh

# Keeping mercurial clone up to date.
if [ -d lib/atlatl ]
then
    hg pull lib/atlatl/
    hg up lib/atlatl/
else
    curl -s http://atlatl.etenil.net/latest.gz | gunzip - > lib/atlatl.php
fi

