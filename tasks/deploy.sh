#!/usr/bin/env bash

source ./tasks/messenger.sh

if [[ -d "~/laravel-craftsman" ]]; then
    mkdir ~/laravel-craftsman
fi
cp -r builds/ ~/laravel-craftsman/
#mv ~/laravel-craftsman/laravel-craftsman ~/laravel-craftsman/craftsman

printf "\n"
printf "==> deploying to: ~/laravel-craftsman\n"
printf "\n"

success "Laravel Craftsman $@ Deployed Successfully" " SUCCESS "
