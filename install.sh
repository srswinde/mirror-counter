#!/bin/bash

apt-get remove --purge apache2
apt-get install nginx
apt-get install php7.0-fpm
apt-get install postgresql-server-dev-9.6

cd db
su postgres
psql -f setup.sql
cp default /etc/nginx/sites-available/default

cp www/* /var/www/html/

service nginx restart
service postgresql start
service php7.0-fpm start
