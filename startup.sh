#!/bin/bash
docker-compose up -d phpwebserver
docker-compose exec phpwebserver php -q /var/www/html/start.php start