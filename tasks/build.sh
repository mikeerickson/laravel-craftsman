#!/usr/bin/env bash

source ./tasks/messenger.sh

BUILD=$(./tasks/bumpBuild.js)
VERSION=$(./tasks/getVersion.js)
BUILD_INFO="v$VERSION build $BUILD"

info "Build number bumped to $BUILD ..." " INFO "

php laravel-craftsman app:build
rm -rf builds/templates
cp -r templates builds/templates
cp config.php builds

printf "\n"
success "Build Completed Successfully" " SUCCESS "

if [[ "$@" == "--deploy" ]]; then
    ./tasks/deploy.sh "$BUILD_INFO"
fi


