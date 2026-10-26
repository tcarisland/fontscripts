#!/usr/bin/env python3

import argparse
import html
import json
import os
import re
import subprocess
import sys
from urllib.error import URLError
from urllib.request import Request, urlopen

BASE_URL = "https://www.dafont.com/authors.php"
SCANDINAVIA = {
    "denmark": {"code": 59, "name": "Denmark"},
    "norway": {"code": 167, "name": "Norway"},
    "sweden": {"code": 212, "name": "Sweden"},
}


def fetch_country_index():
    """Return a mapping of country code -> name from dafont's authors page."""
    try:
        request = Request(BASE_URL, headers={"User-Agent": "fontscripts/collectall"})
        with urlopen(request, timeout=30) as response:
            page = response.read().decode("utf-8", errors="ignore")
    except URLError:
        return {entry["code"]: entry["name"] for entry in SCANDINAVIA.values()}

    matches = re.findall(
        r'(?i)href=["\'](?:https?://(?:www\.)?dafont\.com/)?authors\.php\?cc=(\d+)(?:&[^"\']*)?["\'][^>]*>(.*?)<',
        page,
    )

    index = {}
    for code, label in matches:
        clean = re.sub(r"<.*?>", "", label)
        clean = html.unescape(clean).strip()
        if not clean:
            continue
        index[int(code)] = clean

    if not index:
        return {entry["code"]: entry["name"] for entry in SCANDINAVIA.values()}

    return index


def target_country_list(args):
    index = fetch_country_index()
    index_by_name = {name.lower(): code for code, name in index.items()}

    targets = []
    for raw in args:
        value = raw.strip().lower()
        if value == "scandinavia":
            for country in SCANDINAVIA.values():
                targets.append((country["code"], country["name"]))
        elif value == "world":
            for code, name in sorted(index.items()):
                targets.append((code, name))
        elif value.isdigit():
            code = int(value)
            name = index.get(code, f"Country-{code}")
            targets.append((code, name))
        else:
            match = None
            for country_name, country in SCANDINAVIA.items():
                if value == country_name or value == country["name"].lower():
                    match = (country["code"], country["name"])
                    break
            if match is None:
                if value in index_by_name:
                    code = index_by_name[value]
                    targets.append((code, index[code]))
                else:
                    raise ValueError(f"Unknown target '{raw}'. Use a country code, scandinavia, or world.")
            else:
                targets.append(match)

    # Deduplicate while preserving order
    seen = set()
    ordered = []
    for code, name in targets:
        key = (code, name)
        if key in seen:
            continue
        seen.add(key)
        ordered.append((code, name))

    return ordered


def run_country(code, name):
    script = (
        "<?php\n"
        "include __DIR__ . '/lib.php';\n"
        "scrape_and_save(%d, %s);\n"
        "?>\n"
    ) % (code, json.dumps(name))
    here = os.path.dirname(os.path.abspath(__file__))
    subprocess.run(["php", "-d", "display_errors=1", "-r", script], cwd=here, check=True)


def parse_args():
    parser = argparse.ArgumentParser(
        description="Collect dafont author stats for scandinavia or the world.",
    )
    parser.add_argument(
        "targets",
        nargs="*",
        help="A country code, country name, scandinavia, or world. Defaults to scandinavia.",
    )
    return parser.parse_args()


def main():
    args = parse_args().targets
    if not args:
        args = ["scandinavia"]

    try:
        targets = target_country_list(args)
    except ValueError as exc:
        print(str(exc), file=sys.stderr)
        return 2

    if not targets:
        print("No countries selected.", file=sys.stderr)
        return 2

    for code, name in targets:
        print(f"Collecting {name} ({code})")
        run_country(code, name)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
