#!/bin/sh

mysql=`which mysql`;
if [ "$mysql" = "" ]; then
    mysql=`which mysql5`;
fi

if [ "$mysql" = "" ]; then
    echo "Can not find mysql binary. Is it installed?";
    exit 1;
fi

if [ "$DB_USER" = "" ]; then
	echo "\$DB_USER not set. Using 'root'.";
    DB_USER="root";
fi

pw_option=""
if [ "$DB_PW" != "" ]; then
	pw_option=" -p$DB_PW"
fi

DB_HOSTNAME=${DB_HOSTNAME-127.0.0.1};

"$mysql" --version;

"$mysql" --host="$DB_HOSTNAME" -u"$DB_USER" $pw_option -e '
SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS radisaclaytest;
DROP SCHEMA IF EXISTS migration;
SET FOREIGN_KEY_CHECKS = 1;
'

"$mysql" --host="$DB_HOSTNAME" -u"$DB_USER" $pw_option -e '
CREATE DATABASE radisaclaytest;
CREATE SCHEMA migration;
';
