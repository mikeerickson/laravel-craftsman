#!/usr/bin/env bash

source "./tasks/messenger.sh"

rm -rf app/Http
rm -rf app/Test
rm -rf database/factories
rm -rf database/migrations
rm -rf database/seed

success "Directories Cleaned" " SUCCESS "
