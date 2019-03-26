#!/usr/bin/env bash

source ./tasks/messenger.sh

rm -rf builds/
php laravel-craftsman app:build
rm -rf builds/templates
cp -r templates builds/templates
cp config.php builds

printf "\n"
success "Build Completed Successfully" " SUCCESS "
