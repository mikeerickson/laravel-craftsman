#!/usr/bin/env bash

source ./tasks/messenger.sh

VERSION=$(./tasks/getVersion.js)
printf "\n"
info "Publishing $VERSION ..." " INFO "

printf "\n"

success "✓ Creating Github tag "

git tag "$VERSION" && git push --tags

success "Publishing Completed Successfully " " SUCCESS "
