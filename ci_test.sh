#!/bin/sh

current_date=$(date +'%d-%m-%y')

temp_database_name=test_$current_date-$CI_PROJECT_NAME-$CI_COMMIT_SHORT_SHA
# Берется из CI Variables
export DATABASE_URL=$CI_TEST_DATABASE_URL$temp_database_name

echo "$DATABASE_URL" > .database_url

echo "I: Generated DATABASE_URL: $DATABASE_URL"

composer install --prefer-dist --no-progress --no-interaction

echo "I: Clear tests cache"
bin/console c:c -e test

echo "I: Start tests"
bin/phpunit --migrate-configuration
bin/phpunit --stop-on-failure
if [ $? -ne 0 ]; then
    bin/console doctrine:database:drop -e test -f
    echo "failed test"
    exit 1
fi
bin/console doctrine:database:drop -e test -f

