#!/bin/bash
docker-compose up -d phpwebserver
docker-compose exec phpwebserver php -q /var/www/start.php start