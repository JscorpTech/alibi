#!/bin/bash

php-fpm &
php artisan queue:work --timeout=120

exit $?