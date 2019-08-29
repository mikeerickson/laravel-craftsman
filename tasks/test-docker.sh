# test multiple php versions using docker
source ./tasks/messenger.sh
echo "\n"

info "Testing PHP 7.2 (Alpine Distribution)" " INFO "
docker run -v $(pwd):/app -w /app --rm php:7.2-alpine vendor/bin/phpunit -c phpunit.ci.xml --colors=always
echo "\n"

info "Testing PHP 7.3 (Alpine Distribution)" " INFO "
docker run -v $(pwd):/app -w /app --rm php:7.3-alpine vendor/bin/phpunit -c phpunit.ci.xml --colors=always
echo "\n"

success "Docker Testing Complete" " DONE "
