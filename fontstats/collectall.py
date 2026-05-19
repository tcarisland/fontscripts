#!/usr/bin/env python3

import os

def collect_fontstats(name):
    here = os.path.dirname(__file__)
    script = os.path.join(here, name + '.php')
    os.system('php "' + script + '"')

collect_fontstats('norway')
collect_fontstats('sweden')
collect_fontstats('denmark')
