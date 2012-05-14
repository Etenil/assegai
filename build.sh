#!/bin/sh

# Getting Atlatl
if [ ! -d lib/atlatl ]
then
	hg clone http://pikacode.com/etenil/atlatl lib/atlatl
else
	hg pull lib/atlatl/
	hg up lib/atlatl/
fi
