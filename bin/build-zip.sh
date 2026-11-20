#!/bin/sh
set -eu

ref="${1:-HEAD}"
name="propulse-oxid"

cd "$(dirname "$0")/.."
mkdir -p dist
rm -f "dist/$name.zip"
git archive --format=zip --output="dist/$name.zip" "$ref"
echo "dist/$name.zip"
