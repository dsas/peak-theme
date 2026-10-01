#!/usr/bin/env bash
# Builds dist/peak.zip from the committed HEAD (runtime files only).
set -euo pipefail
cd "$(dirname "$0")/.."
mkdir -p dist
git diff --quiet HEAD || echo "warning: uncommitted changes are not included" >&2
git archive --format=zip --prefix=peak/ -o dist/peak.zip HEAD
unzip -l dist/peak.zip | tail -1
