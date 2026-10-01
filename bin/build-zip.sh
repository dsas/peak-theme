#!/usr/bin/env bash
# Builds dist/peak.zip from the committed HEAD (runtime files only).
set -euo pipefail
cd "$(dirname "$0")/.."
mkdir -p dist
git archive --format=zip --prefix=peak/ -o dist/peak.zip HEAD
unzip -l dist/peak.zip | tail -1
