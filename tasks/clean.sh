#!/usr/bin/env bash

source "./tasks/messenger.sh"

rm -rf app/Http
success " ✔︎ app/Http removed"
rm -rf app/Models
success " ✔︎ app/Models removed"
rm -rf app/Test
success " ✔︎ app/Test removed"
rm -rf database/factories
success " ✔︎ database/factories removed"
rm -rf database/migrations
success " ✔︎ database/migrations removed"
rm -rf database/seeds
success " ✔︎ database/seeds removed"
rm app/Post.php
rm app/TestClass.php

printf "\n"
success "Directories Cleaned Successfully" " SUCCESS "
