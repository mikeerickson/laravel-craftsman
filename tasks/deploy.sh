#!/usr/bin/env bash

APP_PATH="~/laravel-craftsman"

if [[ -d "$APP_PATH" ]]; then
    mkdir ~/laravel-craftsman
fi
cp -r builds/ ~/laravel-craftsman/
mv ~/laravel-craftsman/laravel-craftsman craftsman
