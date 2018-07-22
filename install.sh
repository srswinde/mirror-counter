#!/bin/bash


apt-get remove -y --purge apache2
apt-get update
apt-get install -y nginx
apt-get install -y php7.0-fpm
apt-get install -y postgresql-server-dev-9.6
apt-get install -y postgresql-9.6



sudo -u postgres psql -f db/setup.sql
cp default /etc/nginx/sites-available/default

cp www/* /var/www/html/
cp logger/interrupts.py /usr/local/bin/mcounter-logger.py
cp systemd/logger.service /etc/systemd/system/
chmod 664 /etc/systemd/system/logger.service
systemctl daemon-reload
systemctl enable logger.service


service nginx restart
service postgresql start
service php7.0-fpm start

