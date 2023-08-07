#!/bin/bash
set -e

mkdir -p ./public/assets
if [ -d "./public/assets/" ]; then
    cp -r "./dev/docker/silverstripe/assets/." "./public/assets/"
fi

echo "All assets copied."