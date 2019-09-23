#!/usr/bin/env bash

source ./tasks/messenger.sh

BUILD=$(./tasks/bumpBuild.js)
VERSION=$(./tasks/getVersion.js)
BUILD_INFO="v$VERSION build $BUILD"

info "Build number bumped to $BUILD ..." " INFO "

php laravel-craftsman app:build
rm -rf builds/templates
rm -rf builds/config
cp -r templates builds/templates
mkdir builds/config
cp config/craftsman.php builds/config

printf "\n"
success "Build Completed Successfully" " SUCCESS "

if [[ "$@" == "--deploy" ]]; then
    ./tasks/deploy.sh "$BUILD_INFO"
fi


