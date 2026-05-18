#!/usr/bin/python

import os

def collect_fontstats(country):
    os.system("php " + country + ".php")

collect_fontstats("/home/5/t/tcarisland/scripts/fontstats/norway")
collect_fontstats("/home/5/t/tcarisland/scripts/fontstats/sweden")
collect_fontstats("/home/5/t/tcarisland/scripts/fontstats/denmark")
