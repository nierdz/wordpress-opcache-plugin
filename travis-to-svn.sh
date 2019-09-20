#!/usr/bin/env bash

set -o pipefail
set -o nounset
set -o errexit

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

# Checkout SVN repository
svn co "$SVN_REPOSITORY" ./svn

# Remove trunk and assets folder
rm -rf ./svn/trunk
rm -rf ./svn/asssets

# Copy plugin files and assets files to svn folders
rsync -avz ./flush-opcache/ ./svn/trunk
rsync -avz ./assets/ ./svn/assets

# Commit everything
pushd ./svn
svn add --force trunk
svn add --force assets

# Create SVN tag
svn cp \
  trunk "tags/$TRAVIS_TAG"

# Push to WordPress
svn ci \
  --message "Release $TRAVIS_TAG" \
  --username "$SVN_USERNAME" \
  --password "$SVN_PASSWORD" \
  --non-interactive
