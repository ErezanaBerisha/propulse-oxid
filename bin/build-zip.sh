#!/bin/sh
set -eu

ref="${1:-HEAD}"
name="stathubs-oxid"

cd "$(dirname "$0")/.."
mkdir -p dist
rm -f "dist/$name.zip" "dist/$name.zip.sha256"
git archive --format=zip --output="dist/$name.zip" "$ref"
(cd dist && shasum -a 256 "$name.zip" > "$name.zip.sha256")
echo "dist/$name.zip"
