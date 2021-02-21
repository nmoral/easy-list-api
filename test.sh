#!/usr/bin/env bash

composer dumpautoload


bin/console cache:clear --env=test
bin/console cache:warmup --env=tes

bin/console doctrine:database:drop --if-exists --force --env=test
bin/console doctrine:database:create --if-not-exists --env=test
bin/console doctrine:schema:update -f --env=test

bin/phpunit --configuration phpunit.xml.dist

