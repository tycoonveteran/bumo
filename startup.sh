#!/bin/bash
docker-compose up -d 
docker-compose exec phpwebserver php -q /var/www/html/clientSocket.php
docker-compose logs -f 