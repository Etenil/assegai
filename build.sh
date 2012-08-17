#!/bin/sh

# Keeping mercurial clone up to date.
if [ -d lib/atlatl ]
    hg pull lib/atlatl/
	hg up lib/atlatl/
else  # We work on the compiled atlatl.
    curl http://atlatl.etenil.net/latest.gz | gzcat > lib/atlatl.php
fi
