#!/usr/bin/env bash

set -o nounset
set -o errexit

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

SVN_REPOSITORY=https://plugins.svn.wordpress.org/flush-opcache
SVN_USERNAME=mnttech

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
  trunk "tags/${GITHUB_REF##*/}"

#Remove missing files from svn
missing_files=$(svn status | grep -E "^\!.*$" | awk '{print $2}')
for file in $missing_files; do
  svn rm "$file"
done

# Push to WordPress
svn ci \
  --message "Release ${GITHUB_REF##*/}" \
  --username "$SVN_USERNAME" \
  --password "$SVN_PASSWORD" \
  --non-interactive
