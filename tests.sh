#!/bin/bash
#
# Launch PHPUnit tests, load fixtures and launch CasperJS tests
#
# Param:    url to access the test environment
# Example:  sh test.sh http://localhost/job/app_test.php

phpunit -c app/
echo
php app/console doctrine:fixtures:load --env=test
echo
casperjs test src/SensioLabs/JobBoardBundle/Tests/Casperjs/Homepage.js --url=$1
